<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$oldPassword = $input['old_password'] ?? '';

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
    echo json_encode(['success' => false]);
    exit;
}

echo json_encode(['success' => true]);