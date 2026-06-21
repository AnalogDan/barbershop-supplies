<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';

$count = 0;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(ci.quantity), 0)
        FROM carts c
        JOIN cart_items ci ON ci.cart_id = c.id
        WHERE c.user_id = ?
        AND c.status = 'active'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $count = (int)$stmt->fetchColumn();
} elseif (!empty($_SESSION['cart_id'])) {
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(quantity), 0)
        FROM cart_items
        WHERE cart_id = ?
    ");
    $stmt->execute([(int)$_SESSION['cart_id']]);
    $count = (int)$stmt->fetchColumn();
}

header('Content-Type: application/json');

echo json_encode([
    "count" => $count
]);
