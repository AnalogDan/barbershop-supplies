<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
    // define('BASE_URL', '/barbershopSupplies/public');
    require_once __DIR__ . '/../actions/config.php';

    //Fetch all the thingies you need
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(404);
        die('Invalid product');
    }
    $sql = "
        SELECT 
            p.id,
            p.category_id AS subcategory_id,
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

            c.name AS subcategory_name,
            c.main_category_id,

            mc.name AS main_category_name,
            mc.id   AS main_category_id

        FROM products p
        JOIN categories c 
            ON p.category_id = c.id
        JOIN main_categories mc 
            ON c.main_category_id = mc.id
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

    //Check if already favorited 
    $isFavorite = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("
            SELECT 1
            FROM favorites
            WHERE user_id = ? AND product_id = ?
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id'], $product['id']]);
        $isFavorite = (bool) $stmt->fetchColumn();
    }
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

    .zoom-lens {
        position: absolute;
        border: 2px solid #000;     
        border-radius: 20%;             
        background-repeat: no-repeat;
        background-position: 0 0;
        background-color: rgba(255, 255, 255, 0.1); 
        display: none;
        cursor: none;                  
        pointer-events: none;          
        box-shadow: 0 0 10px rgba(0,0,0,0.5); 
        transition: opacity 0.2s ease;
        z-index: 10;                     
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
    outline: none !important;
    box-shadow: none !important;
    }
    #add-to-cart-btn {
        position: relative;
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

    /*Heart overlay*/
    .heart-wrapper {
        position: absolute; /* relative to main-image */
        top: 10px;
        right: 10px;
        width: 50px;
        height: 50px;
        z-index: 10;
    }
    .heart-wrapper .heart-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #4e4e4eff;
        cursor: pointer;
        border-radius: 50%;
        border: 1px solid black;
        background-color: #ffffffff; 
        transition: background-color 0.3s ease; 
    }
    .heart-wrapper .heart-icon:hover {
        background-color: #d8d8d8ff; 
    }

    /*Favorite message*/
    .added-message {
	position: absolute;
	top: 130%;
    left: 50%;
	transform: translateX(-50%);
	font-weight: 600;
	background: #dfd898;
	color: #000000ff;
	padding: 5px 10px;
	border-radius: 5px;
	font-size: 0.85rem;
	opacity: 0;
	pointer-events: none;
	transition: opacity 0.5s ease, transform 0.5s ease;
	z-index: 10;
	white-space: nowrap;
	text-align: center;  
	}
	.added-message.show {
	opacity: 1;
	transform: translateX(-50%) translateY(-10px);
	}

</style>

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
    <main>
        <!-- <div class="breadcrumb">
            <a href="#">Shop</a>
            <span>&gt;</span>
            <a href="#">Tools &amp; Electricals</a>
            <span>&gt;</span>
            <a href="#">Trimmers</a>
            <span>&gt;</span>
            <span>Andis Slimline Pro Chrome Trimmer</span>
        </div> -->
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/shop.php">Shop</a>
            <span>&gt;</span>

            <a href="<?= BASE_URL ?>/shop.php?main=<?= (int)$product['main_category_id'] ?>&page=1">
                <?= htmlspecialchars($product['main_category_name']) ?>
            </a>
            <span>&gt;</span>

            <a href="<?= BASE_URL ?>/shop.php?subcategory=<?= (int)$product['subcategory_id'] ?>&page=1">
                <?= htmlspecialchars($product['subcategory_name']) ?>
            </a>
            <span>&gt;</span>

            <span><?= htmlspecialchars($product['name']) ?></span>
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
                        <div class="heart-wrapper">
                            <i
                                class="heart-icon fa-heart <?= $isFavorite ? 'fa-solid' : 'fa-regular' ?>"
                                data-product-id="<?= $product['id'] ?>"
                            ></i>
                        </div>
                        <div class="zoom-lens"></div>
                    </div>
                    <div class="gallery">
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
                <div class="quantity-selector" data-stock="<?= (int)$product['stock'] ?>">
                    <button class="qty-btn" id="qty-minus">−</button>
                    <input type="text" id="qty-input" value="1">
                    <button class="qty-btn" id="qty-plus">+</button>
                </div>
                <p><button class="btn btn-secondary me-2 my-btn-custom" id="add-to-cart-btn" data-product-id="<?= (int)$product['id'] ?>">
                    Add to cart</button>
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
            //Add to cart
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const qtyInput = document.getElementById('qty-input');
            addToCartBtn.addEventListener('click', () => {
                const productId = addToCartBtn.dataset.productId;
                const quantity = parseInt(qtyInput.value, 10);

                fetch('/barbershopSupplies/actions/cart-add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        console.error(data.message);
                        return;
                    }

                    console.log(`Product ID: ${data.product_id}`);
                    console.log(`Quantity in cart: ${data.quantity}`);
                    console.log(`Action to the cart: ${data.message}`);
                    showMessage2(addToCartBtn, 'Added to cart!');
                    // here you can show a toast / message
                })
                .catch(err => console.error('Fetch error:', err));
            });

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

                let minZoom = 2.5;  
                let maxZoom = 4;    
                const imageArea = rect.width * rect.height;
                const referenceArea = 200 * 200; 
                let zoom = Math.min(maxZoom, Math.max(minZoom, minZoom + (referenceArea - imageArea) / (referenceArea * 0.5)));

                let x = e.clientX - rect.left - lensSize / 2;
                let y = e.clientY - rect.top - lensSize / 2;

                if (x < 0) x = 0;
                if (y < 0) y = 0;
                if (x > rect.width - lensSize) x = rect.width - lensSize;
                if (y > rect.height - lensSize) y = rect.height - lensSize;

                lens.style.left = x + 'px';
                lens.style.top = y + 'px';

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
            const container = document.querySelector('.quantity-selector');
            const stock = parseInt(container.dataset.stock, 10);
            const minus = document.getElementById('qty-minus');
            const plus = document.getElementById('qty-plus');
            const input = document.getElementById('qty-input');

            function normalizeQuantity(value) {
                let qty = parseInt(value, 10);
                if (isNaN(qty) || qty < 1) qty = 1;
                if (qty > stock) qty = stock;
                return qty;
            }
            plus.addEventListener('click', () => {
                input.value = normalizeQuantity(Number(input.value) + 1);
            });
            minus.addEventListener('click', () => {
                input.value = normalizeQuantity(Number(input.value) - 1);
            });

            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/g, '');
            });

            input.addEventListener('blur', () => {
                let value = parseInt(input.value, 10);
                if (isNaN(value) || value < 1) {
                    input.value = 1;
                    return;
                }
                if (value > stock) {
                    input.value = stock;
                }
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

            //Heart behavior
            const heart = document.querySelector('.heart-icon');
            heart.addEventListener('click', (event) => {
                event.stopPropagation(); 
                const isAdding = heart.classList.contains('fa-regular');
                const productId = heart.dataset.productId;

                fetch(`/barbershopSupplies/actions/favorite.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        action: isAdding ? 'add' : 'remove'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                    if (!data.success) {
                        console.error(data.message);
                        return;
                    }
                    heart.classList.toggle('fa-regular');
                    heart.classList.toggle('fa-solid');
                    console.log(data.message);
                    showFavoriteMessage(heart, isAdding);
                })
                .catch(err => console.error('Fetch error:', err));
            });

            function showFavoriteMessage(heart, isAdding) {
                const message = document.createElement('span');
                message.className = 'added-message';
                message.textContent = isAdding
                    ? 'Added to favorites!'
                    : 'Removed from favorites!';
                heart.parentElement.appendChild(message);
                void message.offsetWidth;
                message.classList.add('show');
                setTimeout(() => {
                    message.classList.remove('show');
                    setTimeout(() => {
                        message.remove();
                    }, 500);
                }, 2000);
            }
            function showMessage2(targetElement, messageText) {
                const message = document.createElement('span');
                message.className = 'added-message';
                message.textContent = messageText;
                targetElement.appendChild(message);

                // Force reflow to allow transition
                void message.offsetWidth;
                message.classList.add('show');

                setTimeout(() => {
                    message.classList.remove('show');
                    setTimeout(() => {
                        message.remove();
                    }, 500);
                }, 2000);
            }
		</script>
    </main>
	</body>
</html>