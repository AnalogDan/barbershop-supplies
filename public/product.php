<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
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
    background: lightgreen;
    }
    .bottom-full {
    clear: both;             
    width: 100%;
    background: lightcoral; 
    padding: 1rem;
    box-sizing: border-box;
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
    margin: 0 auto;
    border: 1px solid black;   
    aspect-ratio: 1 / 1;       
    width: 80%;               
    display: flex;            
    align-items: center;
    justify-content: center;
    overflow: hidden;          
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
    .product-images .gallery img:hover {
    border-color: #555;       
    }

    .main-image {
    position: relative;  
    overflow: hidden;     
    }
    .zoom-lens {
    position: absolute;
    border: 1px solid #000;   
    width: 100px;              
    height: 100px;            /
    background: rgba(255, 255, 255, 0.2); 
    display: none;             
    cursor: pointer;
    pointer-events: none;     
    border-radius: 50%;        
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
                    <img src="images/products/main_1756950792_0cdafe55.webp" alt="Main Product">
                    <div class="zoom-lens"></div>
                </div>
                <div class="gallery">
                    <img src="images/products/main_1756950792_0cdafe55.webp" alt="Gallery 1">
                    <img src="images/products/gallery_1757007537_44f0142a.png" alt="Gallery 2">
                    <img src="images/products/gallery_1757007537_55cd8492.png" alt="Gallery 3">
                    <img src="images/products/gallery_1756953863_8d5e1d93.jpg" alt="Gallery 4">
                </div>
                </div>
            </div>

            <div class="top-right">
            Right half content
            </div>

            <div class="bottom-full">
            Bottom full-width content
            </div>
        </div>
        
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            const mainImg = document.getElementById('product-main');
            const lens = document.querySelector('.zoom-lens');

            mainImg.addEventListener('mousemove', moveLens);
            mainImg.addEventListener('mouseenter', () => lens.style.display = 'block');
            mainImg.addEventListener('mouseleave', () => lens.style.display = 'none');

            function moveLens(e) {
            const rect = mainImg.getBoundingClientRect();
            let x = e.clientX - rect.left - lens.offsetWidth / 2;
            let y = e.clientY - rect.top - lens.offsetHeight / 2;

            // keep lens inside image
            if (x < 0) x = 0;
            if (y < 0) y = 0;
            if (x > rect.width - lens.offsetWidth) x = rect.width - lens.offsetWidth;
            if (y > rect.height - lens.offsetHeight) y = rect.height - lens.offsetHeight;

            lens.style.left = x + 'px';
            lens.style.top = y + 'px';

            // update background of lens to show zoomed image
            lens.style.backgroundImage = `url(${mainImg.src})`;
            lens.style.backgroundSize = `${mainImg.width * 2}px ${mainImg.height * 2}px`; // 2x zoom
            lens.style.backgroundPosition = `-${x * 2}px -${y * 2}px`;
            }
		</script>
    </main>
	</body>
</html>