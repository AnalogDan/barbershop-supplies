<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    header('Content-Type: application/json');

    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID missing']);
        exit;
    }

    $id = intval($_POST['id']);

    $stmt = $pdo->prepare("DELETE FROM gift_cards WHERE id = ?");
    $success = $stmt->execute([$id]);

    echo json_encode(['success' => $success]);
?>