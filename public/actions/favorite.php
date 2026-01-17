<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Unknown error'
];

// Auth check
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'redirect' => '/barbershopSupplies/public/login.php',
        'message' => 'Please log in to continue'
    ]);
    exit;
}

//Read JSON body
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $response['message'] = 'Invalid JSON';
    echo json_encode($response);
    exit;
}
$productId = $data['product_id'] ?? null;
$action    = $data['action'] ?? null;
if (!$productId || !in_array($action, ['add', 'remove'], true)) {
    $response['message'] = 'Invalid parameters';
    echo json_encode($response);
    exit;
}
date_default_timezone_set('America/Los_Angeles');

$userId = $_SESSION['user_id'];

//Modify database
try {
    if ($action === 'add') {
        $createdAt = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO favorites (user_id, product_id, created_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $productId, $createdAt]);
    }

    if ($action === 'remove') {
        $stmt = $pdo->prepare("
            DELETE FROM favorites
            WHERE user_id = ? AND product_id = ?
        ");
        $stmt->execute([$userId, $productId]);
    }

    $response['success'] = true;
    $response['message'] = 'Favorite updated';

} catch (PDOException $e) {
    $response['message'] = 'Database error';
}

echo json_encode($response);
exit;