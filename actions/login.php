<?php
require_once __DIR__ . '/../includes/db.php';
session_start();
header('Content-Type: application/json');

//Validate data
if (empty($_POST['email']) || empty($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required.'
    ]);
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

//Email exists? Password? Active? In that order
$stmt = $pdo->prepare("SELECT id, email, password_hash, is_active, first_name FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Account not found.'
    ]);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Incorrect password.'
    ]);
    exit;
}

if ((int)$user['is_active'] !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Please activate your account before logging in.'
    ]);
    exit;
}


//Remember session token (30 days open)
$token = bin2hex(random_bytes(32)); 
setcookie(
    "rememberme",
    $token,
    time() + (86400 * 30), 
    "/",
    "",
    true,
    true
);
$hashedToken = hash('sha256', $token);
$stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
$stmt->execute([$hashedToken, $user['id']]);

//Store session data and send response
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_first_name'] = $user['first_name'];

echo json_encode([
    'success' => true,
    'message' => 'Login successful!'
]);
exit;

?>