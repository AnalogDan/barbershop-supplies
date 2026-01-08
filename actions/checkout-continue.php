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

// Potentially create session and save data 
if (!isset($_SESSION['checkout'])) {
    $_SESSION['checkout'] = [
        'steps' => []
    ];
}
$_SESSION['checkout']['steps'][$step] = $data;

echo json_encode([
    'success' => true,
    'saved_step' => $step,
    'session_checkout' => $_SESSION['checkout']
]);
exit;