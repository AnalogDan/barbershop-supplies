<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo 'Method Not Allowed';
        exit;
    }
    $product_id = intval($_POST['id'] ?? 0);
    if ($product_id === 0){
        http_response_code(400);
        echo 'Invalid product ID.';
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

    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/public/images/products/';
    $rawThumbnail = $_POST['existingThumbnail'] ?? '';
    $rawMainImg = $_POST['existingMainImg'] ?? '';
    $thumbnailPath = str_replace('/barbershopSupplies/public/', '', $rawThumbnail);
    $mainImagePath = str_replace('/barbershopSupplies/public/', '', $rawMainImg);
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
   
    $existingGallery = $_POST['existingGallery'] ?? [];
    if(!is_array($existingGallery)){
        $existingGallery = [$existingGallery];
    }
    $stmt = $pdo->prepare("SELECT image_path FROM product_gallery_images WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $currentGallery = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($currentGallery as $imagePath){
        if (!in_array($imagePath, $existingGallery, true)){
            $delStmt = $pdo->prepare("DELETE FROM product_gallery_images WHERE product_id = ? AND image_path = ?");
            $delStmt->execute([$product_id, $imagePath]);
            $fileToDelete = $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/public/' . $imagePath;
            if(file_exists($fileToDelete)){
                unlink($fileToDelete);
            }
        }
    }
    $galleryPaths = [];
    if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][1])) {
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
            UPDATE products 
            SET category_id = :category_id,
                name = :name,
                description = :description,
                price = :price,
                stock = :stock,
                main_image = :main_image,
                cutout_image = :cutout_image
            WHERE id = :product_id
            
        ");
        $stmt->execute([
            ':category_id' => $category_id,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':main_image' => $mainImagePath,
            ':cutout_image' => $thumbnailPath,
            ':product_id' => $product_id
        ]);
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
                ':product_id' => $product_id,
                ':image_path' => $path
            ]);
        }
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
?>