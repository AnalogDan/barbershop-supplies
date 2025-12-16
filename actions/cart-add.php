<?php
//header and default response
session_start();
require_once __DIR__ . '/../includes/db.php';
date_default_timezone_set('America/Los_Angeles');
$createdAt = date('Y-m-d H:i:s');
header('Content-Type: application/json');
$response = [
    'success' => false,
    'message' => 'Unknown error'
];

//Product exists? Fetch stock
$stmt = $pdo->prepare("
    SELECT stock
    FROM products
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    $response['message'] = 'Product not found';
    echo json_encode($response);
    exit;
}
$stock = (int) $product['stock'];

//Guest cart exists? 
if (!isset($_SESSION['cart_id'])) {
    $stmt = $pdo->prepare("
        INSERT INTO carts (user_id, created_at)
        VALUES (NULL, ?)
    ");
    $stmt->execute([$created_at]);
    $_SESSION['cart_id'] = $pdo->lastInsertId();
}
$cartId = $_SESSION['cart_id'];

echo json_encode($response);
exit;