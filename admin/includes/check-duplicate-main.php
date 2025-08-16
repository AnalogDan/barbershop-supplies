<?php
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    if (!isset($_POST['name'])) {
        echo json_encode(['exists' => false, 'error' => 'Missing name parameter']);
        exit;
    }

    $name = trim($_POST['name']);

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM main_categories WHERE name = :name");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        echo json_encode(['exists' => $count > 0]);
    } catch (PDOException $e) {
        echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
    }
?>