<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/pricing.php';
header('Content-Type: application/json');
$tz = new DateTimeZone('America/Los_Angeles');
ini_set('display_errors', 0);
error_reporting(0);

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
require_once __DIR__ . '/cart-resolver.php';
$cartId = getActiveCartId($pdo);
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

//Create order snapshot the webhook can rely on 
require_once __DIR__ . '/../../vendor/autoload.php'; 
$stripeConfig = require __DIR__ . '/../../config/stripe.php';
\Stripe\Stripe::setApiKey($stripeConfig['secret_key']);
$now = (new DateTime('now', $tz))->format('Y-m-d H:i:s');
$expiresAt = (new DateTime('now', $tz))->modify('+1 hour')->format('Y-m-d H:i:s');
try {
    $stmt = $pdo->prepare("
        INSERT INTO checkout_sessions 
        (cart_id, user_id, email, subtotal, sales_tax, shipping_cost, 
        total, status, order_id, expires_at, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $cartId,
        $userId ?? null,
        $step1['email'],
        $subtotal,
        $salesTax,
        $shipping,
        $total,
        'pending',
        null,      
        $expiresAt, 
        $now,      
        $now        
    ]);
    $checkoutSessionId = $pdo->lastInsertId();

    // Create Stripe Checkout session
    $stripeSession = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'customer_email' => $step1['email'],
        'metadata' => [
            'checkout_sessions_id' => $checkoutSessionId
        ],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'New Vision Barbershop Supplies Order',
                ],
                'unit_amount' => (int) round($total * 100),
            ],
            'quantity' => 1,
        ]],
        'success_url' => 'http://localhost/barbershopSupplies/public/process-success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => 'http://localhost/barbershopSupplies/public/checkout.php'
    ]);
    http_response_code(303);
    $stmt = $pdo->prepare("
        UPDATE checkout_sessions
        SET stripe_session_id = ?
        WHERE id = ?
    ");
    $stmt->execute([
    $stripeSession->id,
    $checkoutSessionId
    ]);

    echo json_encode([
        'success' => true,
        'publicKey' => $stripeConfig['publishable_key'], 
        'stripeSessionId' => $stripeSession->id,
        'stripeUrl' => $stripeSession->url
    ]);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Stripe error: ' . $e->getMessage()
    ]);
    exit;
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
    exit;
}

