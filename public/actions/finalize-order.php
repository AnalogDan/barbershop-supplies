<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/pricing.php';
$tz = new DateTimeZone('America/Los_Angeles');
$now = (new DateTime('now', $tz))->format('Y-m-d H:i:s');

if (!isset($checkoutSessionId)) {
    // fallback for testing in browser
    $checkoutSessionId = $_GET['session_id'] ?? null;
}
if (!$checkoutSessionId) {
    throw new Exception('Missing checkout session ID.');
}

try {
    $pdo->beginTransaction();
    // Fetch correct checkout_session row and meke sure it's paid
    $stmt = $pdo->prepare("
        SELECT *
        FROM checkout_sessions
        WHERE id = ?
        FOR UPDATE
    ");
    $stmt->execute([$checkoutSessionId]);
    $checkout = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$checkout || $checkout['status'] !== 'paid') {
        throw new Exception('Checkout session not found or not paid.');
    }

    // Prevent duplicate orders
    if ($checkout['order_id']) {
        $pdo->commit();
        return; 
    }

    // Create order
    $orderNumber = strtoupper(bin2hex(random_bytes(4)));
    $placedAt = $now;
    $stmt = $pdo->prepare("
        INSERT INTO orders
        (user_id, address_id, number, subtotal, sales_tax, shipping_cost, total, payment_method, status, placed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'processing', ?)
    ");
    $addressId = 17;
    $stmt->execute([
        $checkout['user_id'],
        $addressId,
        $orderNumber,
        $checkout['subtotal'],
        $checkout['sales_tax'],
        $checkout['shipping_cost'],
        $checkout['total'],
        'stripe',
        $placedAt
    ]);
    $orderId = $pdo->lastInsertId();

    //Fetch cart items
    $stmt = $pdo->prepare("
        SELECT ci.product_id, ci.quantity, p.name, p.price, p.sale_price, p.sale_start, p.sale_end
        FROM cart_items ci
        JOIN carts c ON c.id = ci.cart_id
        JOIN products p ON p.id = ci.product_id
        WHERE ci.cart_id = ? AND c.status = 'active'
    ");
    $stmt->execute([$checkout['cart_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$items) {
        throw new Exception('Cart is empty.');
    }

    // Check stock for race, and compute sale price
    foreach ($items as &$item) {
        $stmtCheck = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
        $stmtCheck->execute([$item['product_id']]);
        $product = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            throw new Exception('Product no longer exists: ' . $item['product_id']);
        }
        if ((int)$product['stock'] < (int)$item['quantity']) {
            throw new Exception('Out of stock: ' . $item['name']);
        }
        $pricing = getProductPricing($item, $tz);
        $item['final_price'] = $pricing['final_price'];
    }

    // Decrement stock
    $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    foreach ($items as $item) {
        $stmtUpdateStock->execute([$item['quantity'], $item['product_id']]);
    }

    // Insert order_items
    $stmtInsertItems = $pdo->prepare("
        INSERT INTO order_items
        (order_id, product_id, product_name, price, quantity)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($items as $item) {
        $stmtInsertItems->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['final_price'],
            $item['quantity']
        ]);
    }

    // Mark cart as completed
    $stmt = $pdo->prepare("UPDATE carts SET status = 'completed' WHERE id = ?");
    $stmt->execute([$checkout['cart_id']]);

    //Kill all session references
    unset($_SESSION['cart_id']);


    //Update checkout_sessions with success_token and order id
    $successToken = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("
        UPDATE checkout_sessions
        SET order_id = ?, success_token = ?, updated_at = ?
        WHERE id = ?
    ");
    $stmt->execute([$orderId, $successToken, $now, $checkoutSessionId]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Finalize order failed: ' . $e->getMessage());
    throw $e; // webhook can handle 500
}