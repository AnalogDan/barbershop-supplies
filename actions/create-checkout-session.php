<?php

header('Content-Type: application/json');

$config = require __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../vendor/autoload.php';

\Stripe\Stripe::setApiKey($config['secret_key']);

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['total']) || $input['total'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid total']);
    exit;
}

$amount = (int) round($input['total'] * 100);

try {
    $session = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Barbershop Order',
                ],
                'unit_amount' => $amount,
            ],
            'quantity' => 1,
        ]],
        'success_url' => 'http://localhost/barbershopSupplies/public/stripe-success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => 'http://localhost/barbershopSupplies/public/checkout.php',
    ]);

    echo json_encode(['url' => $session->url]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}