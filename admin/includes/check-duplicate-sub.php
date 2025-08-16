<?php
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    if (!isset($_POST['name'], $_POST['main_category_id'])) {
        echo json_encode(['exists' => false, 'error' => 'Missing parameters']);
        exit;
    }

    $name = trim($_POST['name']);
    $mainCategoryId = (int) $_POST['main_category_id'];

    try {
        // Check if a subcategory with the same name exists under this main category
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        echo json_encode(['exists' => $count > 0]);
    } catch (PDOException $e) {
        echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
    }
?>