<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');
$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    echo json_encode(['status' => 'error']);
    exit;
}

//Get important data drom checkout_sessions
$stmt = $pdo->prepare("
    SELECT status, order_id, success_token
    FROM checkout_sessions
    WHERE stripe_session_id = ?
    LIMIT 1
");
$stmt->execute([$sessionId]);
$checkout = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => $checkout['status'] ?? 'pending',
    'order_id' => $checkout['order_id'] ?? null,
    'token' => $checkout['success_token'] ?? null
]);


// $row = $stmt->fetch(PDO::FETCH_ASSOC);

// if (!$row) {
//     echo json_encode(['status' => 'error']);
//     exit;
// }

// //If paid, return success json, pending if otherwise 
// if ($row['status'] === 'paid') {
//     echo json_encode([
//         'status' => 'paid',
//         'redirect' =>
//             '/barbershopSupplies/public/success.php'
//             . '?order_id=' . $row['order_id']
//             . '&token=' . $row['success_token']
//     ]);
//     exit;
// }

// echo json_encode(['status' => 'pending']);