<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';
$tz = new DateTimeZone('America/Los_Angeles');
$now = (new DateTime('now', $tz))->format('Y-m-d H:i:s');

$endpointSecret = 'whsec_XXXXXXXXXXXX'; // replace later

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

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    if ($session->payment_status === 'paid') {
        $checkoutSessionId = $session->metadata->checkout_sessions_id ?? null;
        if ($checkoutSessionId) {
            $stmt = $pdo->prepare("
                UPDATE checkout_sessions
                SET status = 'paid', updated_at = ?
                WHERE id = ?
            ");
            $stmt->execute([$now, $checkoutSessionId]);
        }
    }
}

http_response_code(200);