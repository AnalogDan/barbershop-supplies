<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['product_id'], $input['new_stock'])) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit;
    }

    $productId = intval($input['product_id']);
    $newStock = intval($input['new_stock']);

    try {
        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$newStock, $productId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
?>