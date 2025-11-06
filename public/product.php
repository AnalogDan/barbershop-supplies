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
    .gallery img.selected {
    border: 5px solid #2b2b2bff;
    border-radius: 5%; 
    }
    .product-images .gallery img:hover {
          
    }

    .main-image {
    position: relative;  
    overflow: hidden;     
    }
    .zoom-lens {
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
    .shipping-note {
    font-size: 0.9rem;
    margin-bottom: 10px;
    }
    .extra-note {
    font-size: 0.9rem;
    color: #888;
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
                            <img id="main-image" src="images/products/main_1756950792_0cdafe55.webp" alt="Main Product">
                        </a>
                        <div class="zoom-lens"></div>
                    </div>
                    <div class="gallery">
                        <img class="selected" src="images/products/main_1756950792_0cdafe55.webp" alt="Gallery 1">
                        <img src="images/products/gallery_1757007537_44f0142a.png" alt="Gallery 2">
                        <img src="images/products/gallery_1757007537_55cd8492.png" alt="Gallery 3">
                        <img src="images/products/gallery_1756953863_8d5e1d93.jpg" alt="Gallery 4">
                    </div>
                </div>
            </div>

            <div class="top-right">
                <h1 class="product-title">Andis T-Outliner</h1>
                <p class="stock-status">7 in stock</p>
                <p class="product-price">$74.99</p>
                <p class="shipping-note">Shipping calculated at checkout</p>
                <p class="extra-note">More things I'll add later</p>
            </div>

            <div class="bottom-full">
            Bottom full-width content
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
            mainImg.addEventListener('mousemove', moveLens);
            mainImg.addEventListener('mouseenter', () => lens.style.display = 'block');
            mainImg.addEventListener('mouseleave', () => lens.style.display = 'none');
            function moveLens(e) {
                const rect = mainImg.getBoundingClientRect();
                const lensSize = lens.offsetWidth;
                const zoom = 2; 

                let x = e.clientX - rect.left - lensSize / 2;
                let y = e.clientY - rect.top - lensSize / 2;

                if (x < 0) x = 0;
                if (y < 0) y = 0;
                if (x > rect.width - lensSize) x = rect.width - lensSize;
                if (y > rect.height - lensSize) y = rect.height - lensSize;

                lens.style.left = x + 'px';
                lens.style.top = y + 'px';

                lens.style.backgroundImage = `url(${mainImg.src})`;
                lens.style.backgroundSize = `${rect.width * zoom}px ${rect.height * zoom}px`;

                const xPercent = x / (rect.width - lensSize);
                const yPercent = y / (rect.height - lensSize);

                const bgX = (rect.width * zoom - lensSize) * xPercent;
                const bgY = (rect.height * zoom - lensSize) * yPercent;

                lens.style.backgroundPosition = `-${bgX}px -${bgY}px`;
            }

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

// Open lightbox when main image clicked
document.getElementById('main-image-link').addEventListener('click', e => {
  e.preventDefault();
  const currentSrc = document.getElementById('main-image').src;
  currentIndex = galleryList.findIndex(img => img.src === currentSrc);
  if (currentIndex === -1) currentIndex = 0;
  showLightbox(currentIndex);
});

// Navigation
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

// Helper functions
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
		</script>
    </main>
	</body>
</html>