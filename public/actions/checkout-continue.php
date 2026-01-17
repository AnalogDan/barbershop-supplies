<?php
session_start();
header('Content-Type: application/json');

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

if (isset($input['totals']) && is_array($input['totals'])) {
    $_SESSION['checkout']['totals']['subtotal']  = $input['totals']['subtotal']  ?? $_SESSION['checkout']['totals']['subtotal'];
    $_SESSION['checkout']['totals']['sales_tax'] = $input['totals']['sales_tax'] ?? $_SESSION['checkout']['totals']['sales_tax'];
    $_SESSION['checkout']['totals']['shipping']  = $input['totals']['shipping']  ?? $_SESSION['checkout']['totals']['shipping'];
    $_SESSION['checkout']['totals']['total']     = $input['totals']['total']     ?? $_SESSION['checkout']['totals']['total'];
}

echo json_encode([
    'success' => true,
    'saved_step' => $step,
    'session_checkout' => $_SESSION['checkout']
]);
exit;