<?php
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';
    if (!isset($_POST['id'], $_POST['name'])) {
        http_response_code(400);
        exit('Missing parameters');
    }

    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    try{
        $sql = "UPDATE main_categories SET name = :name WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'id' => $id]);
        
        if($stmt->rowCount() > 0){
            echo "Success";
        } else {
            echo "No changes made";
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
?>