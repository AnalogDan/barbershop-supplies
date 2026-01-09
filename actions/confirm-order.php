<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/pricing.php';
header('Content-Type: application/json');
$tz = new DateTimeZone(timezone: 'America/Los_Angeles');

//Make sure all required fields and data exist
$userId = $_SESSION['user_id'] ?? null;
$checkout = $_SESSION['checkout']['steps'] ?? null;
if (!$checkout || empty($checkout[1]) || empty($checkout[2])) {
    echo json_encode([
        'success' => false,
        'message' => 'Checkout session is incomplete.'
    ]);
    exit;
}
if (empty($_SESSION['cart_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart session missing.'
    ]);
    exit;
}
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!$payload) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON payload.'
    ]);
    exit;
}
$step1 = $payload['step1'] ?? null;
$step2 = $payload['step2'] ?? null;
if (!$step1 || !$step2) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing checkout data.'
    ]);
    exit;
}
$requiredStep2Fields = ['full_name', 'street', 'city', 'state', 'zip'];
foreach ($requiredStep2Fields as $field) {
    if (empty($step2[$field])) {
        echo json_encode([
            'success' => false,
            'message' => 'Incomplete address information.'
        ]);
        exit;
    }
}

//Rebuild and recalculate cart items
$cartId = (int) $_SESSION['cart_id'];
$stmt = $pdo->prepare("
    SELECT
        ci.product_id,
        ci.quantity,
        p.name,
        p.price,
        p.stock,
        p.sale_price,
        p.sale_start,
        p.sale_end
    FROM cart_items ci
    JOIN products p ON p.id = ci.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cartId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($cartItems)) {
    echo json_encode([
        'success' => false,
        'message' => 'Your cart is empty.'
    ]);
    exit;
}
foreach ($cartItems as $item) {  //Protection for last minute stock changes
    $stock    = (int) $item['stock'];
    $quantity = (int) $item['quantity'];

    if ($stock < $quantity) {
        echo json_encode([
            'success' => false,
            'message' => 'One or more items are no longer in stock. Please review your cart.'
        ]);
        exit;
    }
}
$summaryItems = [];
$subtotal = 0;
foreach ($cartItems as $item) {
    $pricing = getProductPricing($item, $tz); 
    $unitPrice = $pricing['final_price'];
    $quantity  = (int) $item['quantity'];
    $lineTotal = $unitPrice * $quantity;
    $summaryItems[] = [
        'product_id' => (int) $item['product_id'],
        'name'       => $item['name'],
        'quantity'   => $quantity,
        'unit_price' => $unitPrice,
        'line_total' => $lineTotal
    ];
    $subtotal += $lineTotal;
}
$TAX_RATE = 0.0925;
$salesTax = round($subtotal * $TAX_RATE, 2);
$shipping = 0; // API later
$total    = $subtotal + $salesTax + $shipping;

try{
    //Create order
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("
        INSERT INTO addresses (full_name, street, city, zip, state)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $checkout['2']['full_name'],
        $checkout['2']['street'],
        $checkout['2']['city'],
        $checkout['2']['zip'],
        $checkout['2']['state']
    ]);
    $addressId = $pdo->lastInsertId();
    function generateOrderNumber(): string{
        return strtoupper(bin2hex(random_bytes(4)));
    }
    $orderNumber = generateOrderNumber();
    $placedAt = (new DateTime('now', $tz))->format('Y-m-d H:i:s');
    $deliveryEtaStart = '2026-01-15';
    $deliveryEtaEnd   = '2026-01-20';
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id,
            address_id,
            number,
            subtotal,
            sales_tax,
            shipping_cost,
            total,
            payment_method,
            placed_at,
            delivery_eta_start,
            delivery_eta_end
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId ?? null,
        $addressId,
        $orderNumber,
        $subtotal,
        $salesTax,
        $shipping,
        $total,
        'pending',     // Stripe later
        $placedAt,
        $deliveryEtaStart,
        $deliveryEtaEnd
    ]);
    $orderId = $pdo->lastInsertId();

    //One last stock check
    foreach ($summaryItems as $item) {
        $stmt = $pdo->prepare("
            SELECT stock
            FROM products
            WHERE id = ?
            FOR UPDATE
        ");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            throw new Exception('Product no longer exists.');
        }
        if ((int)$product['stock'] < $item['quantity']) {
            throw new Exception('One or more products are out of stock.');
        }
    }


    //Insert into order_items and reduce stock
    $stmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id,
            product_id,
            product_name,
            price,
            quantity
        ) VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($summaryItems as $item) {
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['name'],
            $item['unit_price'],
            $item['quantity']
        ]);
    }
    $stmt = $pdo->prepare("
        UPDATE products
        SET stock = stock - ?
        WHERE id = ?
    ");
    foreach ($summaryItems as $item) {
        $stmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);
    }

    //Empty cart
    $stmt = $pdo->prepare("
        DELETE FROM cart_items
        WHERE cart_id = ?
    ");
    $stmt->execute([$cartId]);
    $stmt = $pdo->prepare("
        DELETE FROM carts
        WHERE id = ?
    ");
    $stmt->execute([$cartId]);
    unset($_SESSION['cart_id'], $_SESSION['checkout']);

    //Flag token
    $successToken = bin2hex(random_bytes(8));
    $_SESSION['order_success_token'] = $successToken;

    $pdo->commit();

//Here goes the email sending code

    echo json_encode([
        'success'  => true,
        'order_id' => (int) $orderId,
        'token' => $successToken
    ]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}