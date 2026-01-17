<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
        die('Invalid product ID');
    }
    $productId = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product){
        die('Product not found.');
    }
    $thumbnailUrl = !empty($product['cutout_image']) ? '/barbershopSupplies/public/' . $product['cutout_image'] : '';
    $mainImgUrl = !empty($product['main_image']) ? '/barbershopSupplies/public/' . $product['main_image'] : '';
    $stmt = $pdo->prepare("SELECT image_path FROM product_gallery_images WHERE product_id = ?");
    $stmt->execute([$product['id']]);
    $galleryImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>  

<style>
    .product-form{
        max-width: 500px;
        width: 100%;
        margin: 40px auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .product-form label{
        font-size: 20px;
        font-weight: bold;
        color: black;
        margin-bottom: 5px;
        display: block;
    }
    .product-form input,
    .product-form select,
    .product-form textarea{
        width: 100%;
        height: 45px;
        padding: 10px;
        border: 0.5px solid #000;
        background-color: #e2e2e2;
    }
    .product-form textarea{
        text-align: center;
        text-align: justify;
    }
    .product-form input:focus,
    .product-form textarea:focus{
        outline: none;
        box-shadow: 0 0 0 1px #7f7f7f;
    }
    .upload-circle{
        width: 40px;
        height: 40px;
        background-color: #e2e2e2;
        border-radius: 50%;
        font-size: 30px;
        font-weight: bold;
        color: black;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding-bottom: 3px;
        border: 0.5px solid #000;
    }
    .gallery-strip .upload-circle {
        margin-top: 15px; 
    }

    .image-input{
        position: relative;
        width: 70px;
        height: 70px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .image-input img{
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    .image-input .overlay{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.6);
        opacity: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease;
    }
    .image-input:hover .overlay {
        opacity: 1;
    }
    .edit-icon-btn {
        font-size: 20px;
        color: #333;
        background: none;
        border: none;
        cursor: pointer;
    }

    .gallery-strip{
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
</style>

<form class="product-form" id="productForm" method="POST" enctype="multipart/form-data">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" placeholder="Product name..." value="<?= htmlspecialchars($product['name']) ?>" required>
    <label for="category">Category</label>
    <?php
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <select id="category" name="category" required>
        <option value="">Select a category</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialChars($category['name']) ?>
            </option>
        <?php endforeach ?>
    </select>
    <label for="price">Price</label>
    <input type="number" id="price" name="price" min="0" step="0.01" placeholder="0.00" value="<?= htmlspecialchars($product['price']) ?>" required>
    <label for="stock">Stock</label>
    <input type="number" id="stock" name="stock" min="0" step="1" placeholder="0" value="<?= htmlspecialchars($product['stock']) ?>" required>
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5" placeholder="Enter product description here..."><?= htmlspecialchars($product['description']) ?></textarea>
    
    <label for="thumbnail">Thumbnail cutout</label>
    <div class="image-input" id="thumbnailPreview">
        <img src="<?= htmlspecialchars($thumbnailUrl) ?>" alt="Preview">
        <div class="overlay">
            <button type="button" class="edit-icon-btn">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>
    </div>
    <div class="upload-circle" id="thumbnailCircle" style="display:none;">+</div>
    <input type="file" id="thumbnail" name="thumbnail" accept="image/*" style="display:none;">
    <input type="hidden" name="existingThumbnail" value="<?= htmlspecialchars($thumbnailUrl) ?>">

    <label for="mainImg">Main Image</label>
    <div class="image-input" id="mainImgPreview">
        <img src="<?= htmlspecialchars($mainImgUrl) ?>" alt="Preview">
        <div class="overlay">
            <button type="button" class="edit-icon-btn">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>
    </div>
    <div class="upload-circle" id="mainImgCircle" style="display:none;">+</div>
    <input type="file" id="mainImg" name="mainImg" accept="image/*" style="display:none;">
    <div class="error-message" id="mainImgError" style="color: red; display: none;"></div>
    <input type="hidden" name="existingMainImg" value="<?= htmlspecialchars($mainImgUrl) ?>">

    <label for="gallery">Gallery</label>
    <div class="gallery-strip" id="galleryStrip">
        <?php if(!empty($galleryImages)): ?>
            <?php foreach ($galleryImages as $image): ?>
                <div class="image-input existing-image" data-filename="<?= htmlspecialchars($image['image_path']) ?>">
                    <img src="/barbershopSupplies/public/<?= htmlspecialchars($image['image_path']) ?>" alt="Gallery image">
                    <div class="overlay">
                        <button type="button" class="edit-icon-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>
        <div class="upload-circle" id="galleryCircle">+</div>
    </div>
    <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple style="display:none;">

    <div class="button-row">
        <button type="submit" class="btn btn-third">Confirm changes</button>
        <a href="products.php" class="btn btn-fourth">Cancel</a>
    </div>
    <?php include 'includes/modals.php'; ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
</form>

<script>
    document.getElementById('thumbnailCircle').addEventListener('click', () => {
        document.getElementById('thumbnail').click();
    });
    document.getElementById('thumbnail').addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e){
            const preview = document.getElementById('thumbnailPreview');
            preview.querySelector('img').src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('thumbnailCircle').style.display = 'none';
        }
        reader.readAsDataURL(file);
    });
    document.querySelector('#thumbnailPreview .edit-icon-btn').addEventListener('click', () => {
        document.getElementById('thumbnail').click();
    });

    document.getElementById('mainImgCircle').addEventListener('click', () => {
        document.getElementById('mainImg').click();
    });
    document.getElementById('mainImg').addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e){
            const preview = document.getElementById('mainImgPreview');
            preview.querySelector('img').src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('mainImgCircle').style.display = 'none';
        }
        reader.readAsDataURL(file);
    });
    document.querySelector('#mainImgPreview .edit-icon-btn').addEventListener('click', () => {
        document.getElementById('mainImg').click();
    });

    let galleryFiles = [];
    document.querySelectorAll('.existing-image').forEach(div => {
        const filename = div.dataset.filename;
        galleryFiles.push({existing: true, filename: filename});
        const btn = div.querySelector('.edit-icon-btn');
        btn.addEventListener('click', () => {
            showConfirmModal(
                "Delete image?",
                () => {
                    const index = galleryFiles.findIndex(f => f.existing && f.filename === filename);
                    if (index !== -1){
                        galleryFiles.splice(index, 1);
                    }
                    div.remove();
                },
                () => {}     
            );
        });
    });
    document.getElementById('galleryCircle').addEventListener('click', () => {
        document.getElementById('gallery').click();
    });
    document.getElementById('gallery').addEventListener('change', (event) => {
        const files = event.target.files;
        const strip = document.getElementById('galleryStrip');
        const addCircle = document.getElementById('galleryCircle');
        for(let i = 0; i < files.length; i++){
            const file = files[i];
            galleryFiles.push(file);
            const reader = new FileReader();
            reader.onload = function(e){
                const imageContainer = document.createElement('div');
                imageContainer.classList.add('image-input');
                const img = document.createElement('img');
                img.src = e.target.result;
                const overlay = document.createElement('div');
                overlay.classList.add('overlay');
                const btn = document.createElement('button');
                btn.classList.add('edit-icon-btn');
                btn.type = 'button';
                btn.innerHTML = '<i class ="fas fa-trash"></i>';
                btn.addEventListener('click', () => {
                    showConfirmModal(
                        "Delete image?",
                        () => {
                            const allImages = Array.from(strip.querySelectorAll('.image-input'));
                            const index = allImages.indexOf(imageContainer);
                            if(index !== -1){
                                galleryFiles.splice(index, 1);
                            }
                            imageContainer.remove();
                        },
                        () => {}     
                    );
                });
                overlay.appendChild(btn);
                imageContainer.appendChild(img);
                imageContainer.appendChild(overlay);
                strip.insertBefore(imageContainer, addCircle);
            }
            reader.readAsDataURL(file);
        }
        event.target.value = '';
    });

    document.querySelector('.product-form').addEventListener('submit', function (e) {
        e.preventDefault();
        showConfirmModal(
            "Are you sure?",
            () => sendFormData(this),
            () => {}     
        );
    })
    function showConfirmModal(message, onYes, onNo) {
        const template = document.getElementById('confirmModal');
        const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
        document.body.appendChild(modal);
        modal.querySelector('p').textContent = message;
        modal.classList.add('show');
        const yesBtn = modal.querySelector('#confirmYes');
        const noBtn = modal.querySelector('#confirmNo');
        function cleanup() {
            yesBtn.removeEventListener('click', yesHandler);
            noBtn.removeEventListener('click', noHandler);
            modal.remove();
        }
        function yesHandler() {
            cleanup();
            if (typeof onYes === 'function') onYes();
        }
        function noHandler() {
            cleanup();
            if (typeof onNo === 'function') onNo();
        }
        yesBtn.addEventListener('click', yesHandler);
        noBtn.addEventListener('click', noHandler);
    }

    function showAlertModal(message, onOk){
        const template = document.getElementById('alertModal');
        const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
        document.body.appendChild(modal);
        modal.querySelector('p').textContent = message;
        modal.classList.add('show');
        const okBtn = modal.querySelector('#confirmOk');
        function cleanup() {
            okBtn.removeEventListener('click', okHandler);
            modal.remove();
        }
        function okHandler(){
            cleanup();
            if (typeof onOk === 'function'){ onOk()}
            else{};
        }
        okBtn.addEventListener('click', okHandler);
    }

    function sendFormData(form){
        const formData = new FormData(form);
        galleryFiles.forEach(file => {
            if (file instanceof File){
                formData.append('gallery[]', file);
            }else if (file.existing) {
                formData.append('existingGallery[]', file.filename);
            }
        });
        const galleryInput = document.getElementById('gallery');
        galleryInput.parentNode.removeChild(galleryInput);
        fetch('/barbershopSupplies/admin/includes/products-edit-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success){
                showAlertModal("Product updated successfully!", 
                    () => reload()
                );
            }else{
                alert('Failed to add product: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error: ', error);
            showAlertModal("Something went wrong");
        })
    }

    function reload(){
        // window.location.reload();
        // window.location.href = `products-edit.php?id=${productId}`;
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        window.location.href = `products-edit.php?id=${productId}`;
    }
</script>