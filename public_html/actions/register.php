<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require BASE_PATH . 'phpmailer/vendor/autoload.php';
header('Content-Type: application/json');

function respond($success, $message)
{
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
sendActivationEmail($email, $token);

respond(true, "Account created! Please check your email to activate.");
