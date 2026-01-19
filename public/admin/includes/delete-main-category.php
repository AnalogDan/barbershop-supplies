<?php
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';

    if (!isset($_POST['id'])) {
        http_response_code(400);
        exit('Missing ID');
    }

    $id = (int) $_POST['id'];

    try {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE main_category_id = :id");
        $checkStmt->execute(['id' => $id]);
        $subCount = $checkStmt->fetchColumn();

        if ($subCount > 0) {
            echo "HasSubcategories";
            exit;
        }

        $deleteStmt = $pdo->prepare("DELETE FROM main_categories WHERE id = :id");
        $deleteStmt->execute(['id' => $id]);

        if ($deleteStmt->rowCount() > 0) {
            echo "Success";
        } else {
            echo "Cannot delete category or does not exist";
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo "Database error: " . $e->getMessage();
    }
?>