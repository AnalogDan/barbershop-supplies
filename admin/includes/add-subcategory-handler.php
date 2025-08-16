<?php
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    if (!isset($_POST['name'], $_POST['main_category_id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
        exit;
    }

    $name = trim($_POST['name']);
    $mainCategoryId = (int) $_POST['main_category_id'];

    if ($name === '' || $mainCategoryId === 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid name or main category.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO categories (name, main_category_id) VALUES (:name, :main_id)");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':main_id', $mainCategoryId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Subcategory added successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
?>