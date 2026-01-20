<?php
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $name = trim($_POST['name'] ?? '');
        if ($name === ''){
            echo 'Category name is required';
            exit;
        }
        $stmt = $pdo->prepare("SELECT id FROM main_categories WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()){
            echo 'Category already exists';
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO main_categories (name) VALUES (?)");
        if ($stmt->execute([$name])){
            echo 'Success';
        } else {
            echo 'Database error';
        }
    } else {
        echo 'Invalid request';
    }
?>