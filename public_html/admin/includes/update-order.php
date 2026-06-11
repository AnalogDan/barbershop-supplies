<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config.php';
require_once BASE_PATH . 'includes/db.php';
require_once BASE_PATH . 'includes/mailer.php';
$shippedAt = (new DateTime('now', new DateTimeZone('America/Los_Angeles')))->format('Y-m-d H:i:s');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_number'], $input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$orderNumber = $input['order_number'];
$newStatus = $input['status'];
$trackingNumber = trim($input['tracking_number'] ?? '');

$allowedStatuses = ['delivered', 'in-transit', 'processing', 'canceled', 'failed'];
if (!in_array($newStatus, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

//Check if this is first time setting tracking number
$stmt = $pdo->prepare("
    SELECT id, tracking_number
    FROM orders
    WHERE number = ?
");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    throw new Exception('Order not found.');
}
$firstTrackingNumber = empty($order['tracking_number']) && !empty($trackingNumber);



try {
    $stmt = $pdo->prepare("
        UPDATE orders
        SET
            status = :status,
            tracking_number = :tracking_number,
            shipped_at = CASE
                WHEN shipped_at IS NULL
                    AND :status = 'in-transit'
                    AND :tracking_number <> ''
                THEN :shipped_at
                ELSE shipped_at
            END
        WHERE number = :number
    ");
    $stmt->execute([
        ':status' => $newStatus,
        ':tracking_number' => $trackingNumber,
        ':shipped_at' => $shippedAt,
        ':number' => $orderNumber
    ]);

    //send email
    $trackingEmailSent = false;
    if ($firstTrackingNumber) {
        try {
            sendTrackingEmail($order['id']);
            $trackingEmailSent = true;
        } catch (Exception $e) {
            error_log('Tracking email failed: ' . $e->getMessage());
        }
    }
    echo json_encode([
        'success' => true,
        'tracking_email_sent' => $trackingEmailSent
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $e->getMessage()]);
}
