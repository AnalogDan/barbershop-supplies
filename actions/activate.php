<?php
require_once __DIR__ . '/../includes/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid activation link.");
}

// Find user with this token
$stmt = $pdo->prepare("SELECT id, is_active FROM users WHERE activation_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Invalid or expired token.");
}

if ($user['is_active'] == 1) {
    echo "Your account is already activated. You can log in.";
    exit;
}

// Activate the account
$stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
$stmt->execute([$user['id']]);

echo "Your account has been activated! You can now log in.";
?>