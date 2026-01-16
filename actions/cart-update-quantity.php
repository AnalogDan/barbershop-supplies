<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

//Get correct cartId
require_once __DIR__ . '\cart-resolver.php';
$cartId = getActiveCartId($pdo);;

//Validate data
$data = json_decode(file_get_contents('php://input'), true);
$productId = (int)($data['product_id'] ?? 0);
$quantity  = (int)($data['quantity'] ?? 0);
if ($productId <= 0 || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

/*Sanitize quantity (stock cap), product exists?*/
$stmt = $pdo->prepare("
    SELECT stock
    FROM products
    WHERE id = ?
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}
$finalQty = min($quantity, (int)$product['stock']);

/* 3. Update cart_items */
$stmt = $pdo->prepare("
    UPDATE cart_items
    SET quantity = ?
    WHERE cart_id = ? AND product_id = ?
");
$stmt->execute([$finalQty, $cartId, $productId]);

echo json_encode([
    'success'  => true,
    'quantity' => $finalQty
]);