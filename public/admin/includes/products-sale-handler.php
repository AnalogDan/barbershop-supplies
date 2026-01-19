<?php
    header('Content-Type: application/json');
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }

    $productId = (int) $_POST['id'];

    $sale_price = isset($_POST['sale']) && $_POST['sale'] !== '' ? $_POST['sale'] : NULL;
    $sale_start = isset($_POST['start']) && $_POST['start'] !== '' ? $_POST['start'] : NULL;
    $sale_end   = isset($_POST['end']) && $_POST['end'] !== '' ? $_POST['end'] : NULL;

    $today = date('Y-m-d');

    if ($sale_start && $sale_start < $today) {
        echo json_encode(['success' => false, 'message' => 'Start date cannot be before today.']);
        exit;
    }

    if ($sale_start && $sale_end && $sale_end < $sale_start) {
        echo json_encode(['success' => false, 'message' => 'End date cannot be before start date.']);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE products 
        SET sale_price = :sale_price, sale_start = :sale_start, sale_end = :sale_end 
        WHERE id = :id
    ");

    try {
        $stmt->execute([
            'sale_price' => $sale_price,
            'sale_start' => $sale_start,
            'sale_end'   => $sale_end,
            'id'         => $productId
        ]);

        echo json_encode(['success' => true, 'message' => 'Sale updated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
?>