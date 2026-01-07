<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$newPassword = $input['new_password'] ?? '';

if (strlen($newPassword) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters'
    ]);
    exit;
}

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    UPDATE users
    SET password_hash = ?
    WHERE id = ?
");
$stmt->execute([$hash, $_SESSION['user_id']]);

echo json_encode(['success' => true]);