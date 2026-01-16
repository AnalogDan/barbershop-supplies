<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';
$tz = new DateTimeZone('America/Los_Angeles');
$now = (new DateTime('now', $tz))->format('Y-m-d H:i:s');

$endpointSecret = 'whsec_4b8e793d014f83c11c4addb4eae31b10fd820422e600e67d5405eccc437058b7'; 

$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $endpointSecret
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit;
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit;
}

//Success case
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    if ($session->payment_status === 'paid') {
        $checkoutSessionId = $session->metadata->checkout_sessions_id ?? null;
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("
            UPDATE checkout_sessions
            SET
                status = 'paid',
                success_token = ?,
                updated_at = ?
            WHERE id = ?
            AND status != 'paid'
        ");
        $stmt->execute([$token, $now, $checkoutSessionId]);
        if ($stmt->rowCount() === 1) {
            require __DIR__ . '/finalize-order.php';
        }
    }
}

//Fail case
if ($event->type === 'checkout.session.expired') {
    $session = $event->data->object;
    $checkoutSessionId = $session->metadata->checkout_sessions_id ?? null;
    if ($checkoutSessionId) {
        $stmt = $pdo->prepare("
            UPDATE checkout_sessions
            SET status = 'expired',
                updated_at = ?
            WHERE id = ?
              AND status != 'paid'
        ");
        $stmt->execute([$now, $checkoutSessionId]);
    }
}

http_response_code(200);