<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/shipping.php';
require_once __DIR__ . '/cart-resolver.php';

// Read JSON
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!$input || !isset($input['step'], $input['data'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid payload'
    ]);
    exit;
}
$step = (int) $input['step'];
$data = $input['data'];

// Potentially create session and save data / save totals if in payload
if (!isset($_SESSION['checkout'])) {
    $_SESSION['checkout'] = [
        'steps' => [],
        'totals' => [
            'subtotal'   => 0,
            'sales_tax'  => 0,
            'shipping'   => 0,
            'total'      => 0
        ]
    ];
}
$_SESSION['checkout']['steps'][$step] = $data;

//Calculate shipping quote before step 3
if ($step === 2) {
    $cartId = getActiveCartId($pdo);
    $stmt = $pdo->prepare("
        SELECT
            ci.product_id,
            ci.quantity,
            p.name,
            p.price,
            p.sale_price,
            p.sale_start,
            p.sale_end
        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cartId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $address = $_SESSION['checkout']['steps'][2] ?? [];
    $destination = [
        'street' => $address['street'] ?? '',
        'city'   => $address['city'] ?? '',
        'state'  => $address['state'] ?? '',
        'zip'    => $address['zip'] ?? ''
    ];
    $shippingData = getShippingQuote($cartItems, $destination);
    $_SESSION['shipping_quote'] = $shippingData['rates'];
    error_log(print_r($_SESSION['shipping_quote'], true));
}

if (isset($input['totals']) && is_array($input['totals'])) {
    $_SESSION['checkout']['totals']['subtotal']  = $input['totals']['subtotal']  ?? $_SESSION['checkout']['totals']['subtotal'];
    $_SESSION['checkout']['totals']['sales_tax'] = $input['totals']['sales_tax'] ?? $_SESSION['checkout']['totals']['sales_tax'];
    $_SESSION['checkout']['totals']['shipping']  = $input['totals']['shipping']  ?? $_SESSION['checkout']['totals']['shipping'];
    $_SESSION['checkout']['totals']['total']     = $input['totals']['total']     ?? $_SESSION['checkout']['totals']['total'];
}

echo json_encode([
    'success' => true,
    'saved_step' => $step,
    'session_checkout' => $_SESSION['checkout'],
    'shipping_quote' => $_SESSION['shipping_quote'] ?? null
]);
exit;
