<?php
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['order_number'], $input['status'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $orderNumber = $input['order_number'];
    $newStatus = $input['status'];

    $allowedStatuses = ['delivered', 'in-transit', 'processing', 'canceled', 'failed'];
    if (!in_array($newStatus, $allowedStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE number = :number");
        $stmt->execute([
            ':status' => $newStatus,
            ':number' => $orderNumber
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $e->getMessage()]);
    }
?>