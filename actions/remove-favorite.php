<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM favorites
    WHERE user_id = ? AND product_id = ?
    LIMIT 1
");

$stmt->execute([
    $_SESSION['user_id'],
    $data['product_id']
]);

echo json_encode([
    'success' => true
]);