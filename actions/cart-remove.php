<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

//Get correct cartId
require_once __DIR__ . '\cart-resolver.php';
$cartId = getActiveCartId($pdo);

$data = json_decode(file_get_contents('php://input'), true);
$productId = (int)($data['product_id'] ?? 0);

if (!$productId) {
	echo json_encode(['success' => false, 'message' => 'Invalid product']);
	exit;
}

$stmt = $pdo->prepare("
	DELETE FROM cart_items
	WHERE cart_id = :cart_id
	AND product_id = :product_id
	LIMIT 1
");

$stmt->execute([
	'cart_id' => $cartId,
	'product_id' => $productId
]);

echo json_encode(['success' => true]);