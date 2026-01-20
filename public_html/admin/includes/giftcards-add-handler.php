<?php

    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';


   header('Content-Type: application/json');

    $letters = isset($_POST['letters']) ? strtoupper(trim($_POST['letters'])) : '';
    $numbers = isset($_POST['numbers']) ? trim($_POST['numbers']) : '';
    $value   = isset($_POST['value']) ? (int)$_POST['value'] : 0;

    if (!$letters || !$numbers || !$value) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $code = $letters . $numbers;

    try {
        $sql = "INSERT INTO gift_cards (code, value) VALUES (:code, :value)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':code' => $code, ':value' => $value]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
?>