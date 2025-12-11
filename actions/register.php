<?php
require_once __DIR__ . '/../includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../phpmailer/vendor/autoload.php';
header('Content-Type: application/json');

function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

$first = trim($_POST['first-name'] ?? '');
$last = trim($_POST['last-name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['password_confirm'] ?? '';
date_default_timezone_set('America/Los_Angeles');
$created_at = date('Y-m-d H:i:s');
$is_active = 0;

// Basic backend validation
if (!$first || !$last || !$email || !$password || !$confirm) {
    respond(false, "Missing required fields.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, "Invalid email format.");
}
if ($password !== $confirm) {
    respond(false, "Passwords do not match.");
}

// Check if email already exists, Hash password, Generate activation token
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    respond(false, "Email already registered.");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$token = bin2hex(random_bytes(16));

// Insert new user (inactive)
$stmt = $pdo->prepare("
    INSERT INTO users (first_name, last_name, email, password_hash, created_at, is_active, activation_token)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$first, $last, $email, $hashed, $created_at, $is_active, $token]);

// 7. Send activation email
//Google app pasword (phpmailer-newvisionbarbersupplies@gmail.com) is: ybnc jweo jjje hlde
$activation_link = "http://localhost/barbershopSupplies/actions/activate.php?token=" . $token;
$mail = new PHPMailer(exceptions: true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'newvisionbarbersupplies@gmail.com';
$mail->Password = 'ybncjweojjjehlde';             
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->setFrom('newvisionbarbersupplies@gmail.com', 'New Vision Barber Supplies'); 
$mail->addAddress($email);  
$mail->isHTML(true);
$mail->Subject = "Activate your account";
$mail->Body = "
Please click the link to activate your account:  
<a href='$activation_link'>$activation_link</a>
";
$mail->AltBody = "Activation link: $activation_link";
$mail->send();

respond(true, "Account created! Please check your email to activate.");
?>