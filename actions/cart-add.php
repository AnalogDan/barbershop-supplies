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

//Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$productId = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$quantityToAdd = isset($input['quantity']) ? (int)$input['quantity'] : 1;

if (!$productId || $quantityToAdd < 1) {
    $response['message'] = 'Invalid product or quantity';
    echo json_encode($response);
    exit;
}

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

//Guest cart exists? Create it 
if (!isset($_SESSION['cart_id'])) {
    $stmt = $pdo->prepare("
        INSERT INTO carts (user_id, created_at)
        VALUES (NULL, ?)
    ");
    $stmt->execute([$createdAt]);
    $_SESSION['cart_id'] = $pdo->lastInsertId();
}
$cartId = $_SESSION['cart_id'];

//Is the product already in the cart? Sum it or create it
$stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$cartId, $productId]);
$cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
if ($cartItem) {
    $newQuantity = min($cartItem['quantity'] + $quantityToAdd, $stock);
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$newQuantity, $cartId, $productId]);
    $response['message'] = 'Product quantity updated in cart';
} else {
    $insertQuantity = min($quantityToAdd, $stock);
    $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$cartId, $productId, $insertQuantity]);
    $response['message'] = 'Product added to cart';
}

$response['success'] = true;
$response['product_id'] = $productId;
$response['quantity'] = isset($newQuantity) ? $newQuantity : $insertQuantity;

echo json_encode($response);
exit;