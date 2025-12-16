<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';

    //Fetch all the thingies you need
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(404);
        die('Invalid product');
    }
    $sql = "
        SELECT 
            p.id,
            p.category_id,
            p.slug,
            p.description,
            p.name,
            p.price,
            p.sale_price,
            p.sale_start,
            p.sale_end,
            p.cutout_image,
            p.stock,
            p.main_image,
            c.main_category_id
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        http_response_code(404);
        die('Product not found');
    }

    //Fetch gallery
    $galleryStmt = $pdo->prepare("
        SELECT image_path
        FROM product_gallery_images
        WHERE product_id = ?
        ORDER BY id ASC
    ");
    $galleryStmt->execute([$product['id']]);
    $galleryImages = $galleryStmt->fetchAll(PDO::FETCH_COLUMN);

    //Sales logic
    require_once __DIR__ . '/../includes/pricing.php';
    $tz = new DateTimeZone('America/Los_Angeles');
    $pricing = getProductPricing($product, $tz);
?>

<style>
    main {
        background-color: #d2d2d2ff;
    }
    .white-container {
        background-color: white;     
        width: 90%;                 
        margin: 70px auto 0 auto;              
        min-height: 100vh;           
        padding: 2rem;     
        box-shadow: 5px 10px 12px rgba(0,0,0,0.2); 
        border-radius: 8px;       
    }
    .top-left,
    .top-right {
    width: 50%;              
    float: left;           
    padding: 1rem;          
    box-sizing: border-box;  
    }
    .top-left {
    /* background: lightblue; */
    }
    .top-right {
    }

    .breadcrumb {
    font-weight: 600;
    color: gray;               
    font-size: 1.2rem;
    position: relative; 
    top: 70px;
    left: 5%;          
    }
    .breadcrumb a {
    text-decoration: none;      
    color: gray;                
    }
    .breadcrumb a:hover {
    text-decoration: underline; 
    }
    .breadcrumb span {
    margin: 0 0.25rem;          
    }

    .product-images {
    display: flex;
    flex-direction: column;   
    gap: 1rem;                
    }
    .product-images .main-image {
    position: relative;
    margin: 0 auto;
    border: 1px solid black;
    width: 80%;
    /* width: 50rem; */
    aspect-ratio: 1/1;
    }
    .product-images .main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    }
    .product-images .gallery {
    margin: 0 auto;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;           
    }
    .product-images .gallery img {
    border: 1px solid black;
    width: 70px;               
    height: 70px;
    object-fit: contain;        
    cursor: pointer;
    transition: border-color 0.2s;
    }
    .gallery img.selected {
    border: 5px solid #2b2b2bff;
    border-radius: 5%; 
    }
    .product-images .gallery img:hover {
          
    }

    /* .zoom-lens {
    position: absolute;
    border: 1px solid #000;   
    width: 250px;              
    height: 250px;            /
    background: rgba(255, 255, 255, 0.2); 
    display: none;             
    cursor: pointer;
    pointer-events: none;     
    border-radius: 20%;        
    transition: opacity 1.15s ease;
    opacity: 0;
    } */
    .zoom-lens {
        position: absolute;
        border: 2px solid #000;         /* visible border for the lens */
        /* width: 150px;                   
        height: 150px; */
        border-radius: 20%;             /* circular lens */
        background-repeat: no-repeat;
        background-position: 0 0;
        background-color: rgba(255, 255, 255, 0.1); /* subtle overlay */
        display: none;
        cursor: none;                   /* hide cursor inside lens */
        pointer-events: none;           /* allows mouse events to pass through */
        box-shadow: 0 0 10px rgba(0,0,0,0.5); /* optional: 3D effect */
        transition: opacity 0.2s ease;
        z-index: 10;                     /* make sure it sits above the image */
    }
    .main-image:hover .zoom-lens {
    opacity: 1;
    }

    /* lighbox styles */
    .lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background-color: rgba(0,0,0,0.85);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    }
    .lightbox.show {
    display: flex;
    }
    .lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    }
    #lightbox-image {
    width: 100%;
    height: auto;
    border-radius: 6px;
    box-shadow: 0 0 20px rgba(0,0,0,0.6);
    }
    .close {
    position: fixed;
    top: 20px;
    right: 30px;
    font-size: 40px;
    color: #fff;
    cursor: pointer;
    }
    .chevron {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 60px;
    color: white;
    cursor: pointer;
    padding: 10px;
    opacity: 0.7;
    transition: opacity 0.2s ease;
    }
    .chevron:hover {
    opacity: 1;
    }
    .chevron-left {
    left: -80px;
    }
    .chevron-right {
    right: -80px;
    }
    #lightbox-image {
    max-width: 90vw;          
    max-height: 90vh;         
    width: auto;              
    height: auto;             
    object-fit: contain;      
    border-radius: 6px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
    display: block;
    margin: 0 auto;
    }

    /*Top-right quadrant */
    .top-right {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 30px;
    padding: 20px 30px;
    color: #222;
    }
    .product-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 10px;
    }
    .stock-status {
    font-size: 1.5rem;
    margin-bottom: 10px;
    }
    .product-price {
    font-size: 2rem;
    font-weight: 480;
    margin-bottom: 10px;
    }
    .product-old-price {
    font-size: 1rem;
    font-weight: 480;
    margin-bottom: 10px;
    text-decoration: line-through;
    color: rgba(0, 0, 0, 0.5);
    align-items: flex-end;
    }
    .shipping-note {
    font-size: 0.9rem;
    margin-bottom: 10px;
    }
    .extra-note {
    font-size: 0.9rem;
    color: #888;
    }

    /*Quantity selector*/
    .quantity-selector {
    display: inline-flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 6px;
    overflow: hidden;
    width: fit-content;
    }
    .qty-btn {
    background: #f0f0f0;
    border: none;
    padding: 8px 12px;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.2s;
    }
    .qty-btn:hover {
    background: #e0e0e0;
    }
    #qty-input {
    width: 50px;
    text-align: center;
    border: none;
    font-size: 16px;
    outline: none;
    background: white;
    }

    /*Add to cart tweaks*/
    .my-btn-custom {
    border-radius: 10px !important;
    margin-top: 20px;
    margin-left: -5px;
    }

    /*Description box*/
    .bottom-full {
        clear: both;
        width: 100%;
        padding: 1rem;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;     
        align-items: center;
    }
    .bottom-full > * {
        margin: 0; 
    }
    .desc-toggle {
        margin-top: 40px;
        background: none;
        border: none;
        color: black;
        font-weight: 450;
        font-size: 1.5rem;
        cursor: pointer;
        display: inline-flex;
        gap: 0.4rem; 
        transition: color 0.3s ease;
    }
    .desc-toggle:hover {
        color: #7f7f7fff;
    }
    .chevron2{
        font-size: 2rem;
        display: inline-block;
        transition: transform 0.3s ease;
    }
    .chevron2.down {
        transform: rotate(90deg); 
    }
    .description-box {
        font-size: 1.2rem;
        white-space: pre-wrap;
        overflow: hidden;
        transition: max-height 0.4s ease;
        padding: 0 1rem;
        opacity: 0;
        max-height: 200px;
        opacity: 1;
        position: relative;
        max-width: 80%;
    }
    .description-box::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 60px; 
        background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, white 100%);
        pointer-events: none;
    }
    .description-box.open {
        
    }
    .description-box.open::after {
        display: none;
    }

    /*Discount badge */
    .discount-badge {
        background-color: #dfd898;
        color: #000;
        font-size: 1rem;
        font-weight: 480;
        padding: 0.2rem 0.45rem;
        border-radius: 0.35rem;
        line-height: 1;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
    }
    .price-wrapper{
        display: flex;
        gap: 1rem;
        align-items: baseline;
    }

</style>

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
    <main>
        <div class="breadcrumb">
            <a href="#">Shop</a>
            <span>&gt;</span>
            <a href="#">Tools &amp; Electricals</a>
            <span>&gt;</span>
            <a href="#">Trimmers</a>
            <span>&gt;</span>
            <span>Andis Slimline Pro Chrome Trimmer</span>
        </div>
        
        <div class="white-container">
            <div class="top-left">
                <div class="product-images">
                    <div class="main-image">
                        <a href="#" id="main-image-link">
                            <img
                                id="main-image"
                                src="<?= htmlspecialchars($product['main_image']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                            >
                        </a>
                        <div class="zoom-lens"></div>
                    </div>
                    <div class="gallery">
                        <!-- <img class="selected" src="images/products/main_1756950792_0cdafe55.webp" alt="Gallery 1">
                        <img src="images/products/gallery_1757007537_44f0142a.png" alt="Gallery 2">
                        <img src="images/products/gallery_1757007537_55cd8492.png" alt="Gallery 3">
                        <img src="images/products/gallery_1756953863_8d5e1d93.jpg" alt="Gallery 4"> -->
                        <img
                            class="selected"
                            src="<?= htmlspecialchars($product['main_image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                        >

                        <!-- Gallery images -->
                        <?php foreach ($galleryImages as $img): ?>
                            <img
                                src="<?= htmlspecialchars($img) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                            >
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="top-right">
                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="stock-status"><?= (int)$product['stock'] ?> in stock</p>
                <div class="price-wrapper">
                    <?php if ($pricing['is_on_sale']): ?>
                        <strong class="product-price">
                            $<?= number_format($pricing['final_price'], 2) ?>
                        </strong>
                        <span class="product-old-price">
                            $<?= number_format($pricing['original_price'], 2) ?>
                        </span>
                        <span class="discount-badge">
                            <?= $pricing['discount_percent'] ?>% OFF
                        </span>
                    <?php else: ?>
                        <strong class="product-price">
                            $<?= number_format($pricing['final_price'], 2) ?>
                        </strong>
                    <?php endif; ?>
                </div>
                <p class="shipping-note">Shipping calculated at checkout</p>
                <div class="quantity-selector">
                    <button class="qty-btn" id="qty-minus">−</button>
                    <input type="text" id="qty-input" value="1" readonly>
                    <button class="qty-btn" id="qty-plus">+</button>
                </div>
                <p><a href="#" class="btn btn-secondary me-2 my-btn-custom">Add to cart</a>
            </div>

            <div class="bottom-full">
                <button class="desc-toggle">
                    Description <span class="chevron2">&rsaquo;</span>
                </button>
                <div class="description-box">
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
            </div>
        </div>
        
        <?php 
        include '../includes/footer2.php'
        ?>

        <div id="lightbox" class="lightbox">
            <span class="close">&times;</span>
            <div class="lightbox-content">
                <button class="chevron chevron-left">&#10094;</button>
                <img id="lightbox-image" src="" alt="Expanded product">
                <button class="chevron chevron-right">&#10095;</button>
            </div>
        </div>
        <script>
            //Magnifying glass effect
            const mainImg = document.getElementById('main-image');
            const lens = document.querySelector('.zoom-lens');
            const zoom = 2;
            mainImg.addEventListener('mousemove', moveLens);
            mainImg.addEventListener('mouseenter', () => lens.style.display = 'block');
            mainImg.addEventListener('mouseleave', () => lens.style.display = 'none');
            function moveLens(e) {
                const rect = mainImg.getBoundingClientRect();
                const lensSize = lens.offsetWidth;

                // Adjust zoom factor based on displayed image size
                let minZoom = 2.5;  // base zoom is bigger
                let maxZoom = 4;    // max zoom allowed
                const imageArea = rect.width * rect.height;
                const referenceArea = 200 * 200; // reference size
                let zoom = Math.min(maxZoom, Math.max(minZoom, minZoom + (referenceArea - imageArea) / (referenceArea * 0.5)));

                // Position lens
                let x = e.clientX - rect.left - lensSize / 2;
                let y = e.clientY - rect.top - lensSize / 2;

                if (x < 0) x = 0;
                if (y < 0) y = 0;
                if (x > rect.width - lensSize) x = rect.width - lensSize;
                if (y > rect.height - lensSize) y = rect.height - lensSize;

                lens.style.left = x + 'px';
                lens.style.top = y + 'px';

                // Set zoomed background
                lens.style.backgroundImage = `url(${mainImg.src})`;
                lens.style.backgroundSize = `${mainImg.naturalWidth * zoom}px ${mainImg.naturalHeight * zoom}px`;

                const xPercent = x / (rect.width - lensSize);
                const yPercent = y / (rect.height - lensSize);

                const bgX = (mainImg.naturalWidth * zoom - lensSize) * xPercent;
                const bgY = (mainImg.naturalHeight * zoom - lensSize) * yPercent;

                lens.style.backgroundPosition = `-${bgX}px -${bgY}px`;
            }
            function updateLensSize() {
                const rect = mainImg.getBoundingClientRect();

                const lensWidth = Math.min(Math.max(rect.width * 0.45, 100), 700); 
                const lensHeight = Math.min(Math.max(rect.height * 0.45, 100), 700);

                lens.style.width = lensWidth + 'px';
                lens.style.height = lensHeight + 'px';
            }
            updateLensSize();
            window.addEventListener('resize', updateLensSize);
            
            // function moveLens(e) {
            //     const rect = mainImg.getBoundingClientRect();
            //     const lensSize = lens.offsetWidth;
            //     const zoom = 2; 

            //     let x = e.clientX - rect.left - lensSize / 2;
            //     let y = e.clientY - rect.top - lensSize / 2;

            //     if (x < 0) x = 0;
            //     if (y < 0) y = 0;
            //     if (x > rect.width - lensSize) x = rect.width - lensSize;
            //     if (y > rect.height - lensSize) y = rect.height - lensSize;

            //     lens.style.left = x + 'px';
            //     lens.style.top = y + 'px';

            //     lens.style.backgroundImage = `url(${mainImg.src})`;
            //     lens.style.backgroundSize = `${rect.width * zoom}px ${rect.height * zoom}px`;

            //     const xPercent = x / (rect.width - lensSize);
            //     const yPercent = y / (rect.height - lensSize);

            //     const bgX = (rect.width * zoom - lensSize) * xPercent;
            //     const bgY = (rect.height * zoom - lensSize) * yPercent;

            //     lens.style.backgroundPosition = `-${bgX}px -${bgY}px`;
            // }

            // Switch big image 
            const galleryImages = document.querySelectorAll('.gallery img');
            const mainImage = document.getElementById('main-image');
            galleryImages.forEach(img => {
                img.addEventListener('click', () => {
                    mainImage.src = img.src;
                    galleryImages.forEach(i => i.classList.remove('selected'));
                    img.classList.add('selected');
                });
            });


            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightbox-image');
            const closeBtn = document.querySelector('.lightbox .close');
            const prevBtn = document.querySelector('.chevron-left');
            const nextBtn = document.querySelector('.chevron-right');

            const galleryList = Array.from(document.querySelectorAll('.gallery img'));
            let currentIndex = 0;

            document.getElementById('main-image-link').addEventListener('click', e => {
            e.preventDefault();
            const currentSrc = document.getElementById('main-image').src;
            currentIndex = galleryList.findIndex(img => img.src === currentSrc);
            if (currentIndex === -1) currentIndex = 0;
            showLightbox(currentIndex);
            });

            prevBtn.addEventListener('click', () => changeImage(-1));
            nextBtn.addEventListener('click', () => changeImage(1));
            closeBtn.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', e => {
            if (e.target === lightbox) closeLightbox();
            });
            document.addEventListener('keydown', e => {
            if (!lightbox.classList.contains('show')) return;
            if (e.key === 'ArrowLeft') changeImage(-1);
            if (e.key === 'ArrowRight') changeImage(1);
            if (e.key === 'Escape') closeLightbox();
            });

            function showLightbox(index) {
            lightboxImage.src = galleryList[index].src;
            lightbox.classList.add('show');
            }

            function closeLightbox() {
            lightbox.classList.remove('show');
            }

            function changeImage(direction) {
            currentIndex += direction;
            if (currentIndex < 0) currentIndex = galleryList.length - 1;
            if (currentIndex >= galleryList.length) currentIndex = 0;
            lightboxImage.src = galleryList[currentIndex].src;
            }

            //Quantity selector
            const minus = document.getElementById('qty-minus');
            const plus = document.getElementById('qty-plus');
            const input = document.getElementById('qty-input');
            plus.addEventListener('click', () => {
            input.value = parseInt(input.value) + 1;
            });
            minus.addEventListener('click', () => {
            const current = parseInt(input.value);
            if (current > 1) input.value = current - 1;
            });


            //Show description
            const toggleBtn = document.querySelector('.desc-toggle');
            const chevron = toggleBtn.querySelector('.chevron2');
            const descBox = document.querySelector('.description-box');
            toggleBtn.addEventListener('click', () => {
                const isOpen = descBox.classList.toggle('open');
                chevron.classList.toggle('down');

                if (isOpen) {
                    const fullHeight = descBox.scrollHeight + "px";
                    descBox.style.maxHeight = fullHeight;
                } else {
                    descBox.style.maxHeight = "200px";
                }
            });
		</script>
    </main>
	</body>
</html>