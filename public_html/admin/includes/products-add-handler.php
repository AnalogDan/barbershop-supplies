<?php 
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo 'Method Not Allowed';
        exit;
    }
    $name = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $errors = [];
    if ($name === '') $errors[] = 'Product name is required.';
    if ($category_id === 0) $errors[] = 'Category is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($stock < 0) $errors[] = 'Stock cannot be negative.';
    if ($description === '') $errors[] = 'Description is required.';
    if (!empty($errors)) {
        http_response_code(400);
        echo implode("\n", $errors);
        exit;
    }

    $uploadDir = PUBLIC_PATH . 'images/products/';
    $thumbnailPath = '';
    $mainImagePath = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $newName = 'thumb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
            $thumbnailPath = 'images/products/' . $newName;
        } else {
            http_response_code(500);
            echo 'Failed to upload thumbnail.';
            exit;
        }
    }
    if (isset($_FILES['mainImg']) && $_FILES['mainImg']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['mainImg']['name'], PATHINFO_EXTENSION);
        $newName = 'main_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['mainImg']['tmp_name'], $dest)) {
            $mainImagePath = 'images/products/' . $newName;
        } else {
            http_response_code(500);
            echo 'Failed to upload main image.';
            exit;
        }
    }
    if (empty($thumbnailPath)) {
        $thumbnailPath = $mainImagePath;
    }
    $galleryPaths = [];
    if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['gallery']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                $newName = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = $uploadDir . $newName;
                if (move_uploaded_file($tmpName, $dest)) {
                    $galleryPaths[] = 'images/products/' . $newName;
                } else {
                    http_response_code(500);
                    echo 'Failed to upload one of the gallery images.';
                    exit;
                }
            }
        }
    }

    try{
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, description, price, stock, main_image, cutout_image)
            VALUES (:category_id, :name, :description, :price, :stock, :main_image, :cutout_image)
        ");
        $stmt->execute([
            ':category_id' => $category_id,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':main_image' => $mainImagePath,
            ':cutout_image' => $thumbnailPath
        ]);
        $productId = $pdo->lastInsertId();
    }catch(PDOException $e){
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
        exit;
    }
    if(!empty($galleryPaths)){
        $stmt = $pdo->prepare("
            INSERT INTO product_gallery_images (product_id, image_path)
            VALUES (:product_id, :image_path)
        ");
        foreach($galleryPaths as $path){
            $stmt->execute([
                ':product_id' => $productId,
                ':image_path' => $path
            ]);
        }
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
?>