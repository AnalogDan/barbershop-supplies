<?php
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'], $input['name'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $id = intval($input['id']);
    $name = trim($input['name']);

    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'Name cannot be empty']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Sub-category updated successfully']);
        } else {
            echo json_encode(['success' => true, 'message' => 'No changes made in sub']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
?>