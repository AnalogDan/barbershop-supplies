<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';
    header('Content-Type: application/json');
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = intval($input['id'] ?? 0);

   try{
        $stmt = $pdo->prepare("SELECT image_path FROM product_gallery_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach($images as $image){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/public/' . $image;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $pdo->prepare("SELECT main_image, cutout_image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if($product){
            foreach(['main_image', 'cutout_image'] as $field){
                if(!empty($product[$field])){
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/public/' . $product[$field];
                    if(file_exists($filePath)){
                        unlink($filePath);
                    }
                }
            }
        }

        $delProduct = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delProduct->execute([$product_id]);
        echo json_encode(['success' => true]);
   } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
   }
?>