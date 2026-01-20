<?php
    header('Content-Type: application/json');
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';
    if (!isset($_POST['sub_category_id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing subcategory ID']);
        exit;
    }

    $subCategoryId = (int) $_POST['sub_category_id'];
    try {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = :sub_id");
        $stmtCheck->bindValue(':sub_id', $subCategoryId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $productCount = $stmtCheck->fetchColumn();
        if ($productCount > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'HasProducts'
            ]);
            exit;
        }

        $stmtDelete = $pdo->prepare("DELETE FROM categories WHERE id = :sub_id");
        $stmtDelete->bindValue(':sub_id', $subCategoryId, PDO::PARAM_INT);
        $stmtDelete->execute();

        if ($stmtDelete->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Subcategory not found or already deleted']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

?>