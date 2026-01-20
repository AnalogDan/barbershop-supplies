<?php
    header('Content-Type: application/json');
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';

    if (!isset($_POST['sub_category_id'], $_POST['main_category_id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }

    $subCategoryId = (int) $_POST['sub_category_id'];
    $mainCategoryId = (int) $_POST['main_category_id'];

    try {
        $stmt = $pdo->prepare("UPDATE categories SET main_category_id = :main_id WHERE id = :sub_id");
        $stmt->bindValue(':main_id', $mainCategoryId, PDO::PARAM_INT);
        $stmt->bindValue(':sub_id', $subCategoryId, PDO::PARAM_INT);
        $stmt->execute();

        $rowsAffected = $stmt->rowCount();
        if ($rowsAffected > 0) {
            echo json_encode(['success' => true, 'message' => 'Parent category updated successfully.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'No changes were made.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
?>