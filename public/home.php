<?php
    require_once __DIR__ . '/../includes/db.php';
	require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'home';
?>

<style>
	
	.sales-header {
	padding: 2rem 0 0 0;
	margin-bottom: 10px;
	text-align: center;    
	}
	.sales-header h2 {
	font-family: 'OldLondon', serif;  
	font-size: 3rem;                  
	font-weight: normal;             
	color: #000; 
	position: relative;   
	top: 10px;           
	margin: 0;                                   
	}
	.sales-header img {
	width: 350px;     
	height: auto;     
	display: block;    
	margin: 0 auto !important;   
	}


	.sales-products {
	display: flex;
	gap: 2rem;          
	overflow-x: auto; 
	overflow-y: auto;
	padding-top: 2rem;   
	padding-bottom: 1rem; 
	width: 80%;
	margin: 0 auto;
	}
	.sales-products .product-item {
	text-align: center;
	text-decoration: none;
	display: block;
	position: relative;
	cursor: pointer;
	padding-bottom: 50px; 
	z-index: 1;
	}
	.sales-products .product-item .product-thumbnail {
	width: 100%;
	height: 200px;         
	object-fit: contain;   
	margin-bottom: 1rem;
	position: relative;
	top: 0;
	transition: .3s all ease;
	}
	.sales-products .product-item h3 {
	font-weight: 600;
	font-size: 16px;
	margin: 0.25rem 0;
	}
	.sales-products .product-item strong {
	font-weight: 800;
	font-size: 18px;
	display: block;
	color: #2f2f2f;
	}
	.sales-products .product-item:before {
	content: "";
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	background: #cacaca; 
	height: 0%;
	z-index: -1;
	border-radius: 10px;
	transition: .3s all ease;
	}
	.sales-products .product-item:hover .product-thumbnail {
	top: -25px;
	}
	.sales-products .product-item:hover:before {
	height: 70%;
	}
	.sales-products .product-item .icon-cross {
	position: absolute;
	width: 35px;
	height: 35px;
	display: inline-block;
	background: #2f2f2f;
	bottom: 15px;          
	left: 50%;
	transform: translateX(-50%);
	margin-bottom: -30px; 
	border-radius: 50%;
	opacity: 0;
	visibility: hidden;
	transition: .3s all ease;
	pointer-events: auto;
	}
	.sales-products .product-item:hover .icon-cross {
	opacity: 1;
	visibility: visible;
	}
	.sales-products .product-item .icon-cross img {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	}
	.sales-products .product-item .icon-cross:not(.checkmark):hover {
	background: #7f7f7f;
	transform: translateX(-50%) scale(1.07);
	}
	.sales-products .product-item .icon-cross i {
	pointer-events: none; 
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	}
	.sales-products .product-item .added-message {
	position: absolute;
	bottom: 50px;          /* adjust so it sits right under the product */
	left: 50%;
	transform: translateX(-50%);
	white-space: nowrap;    /* keep text in one line */
	}

	.sales-arrow {
	width: 40px;
	height: 40px;
	border-radius: 50%;
	background: #afafafff;       
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	z-index: 10;
	border: none;            
    box-shadow: none;   
	outline: none; 
	transition: background 0.3s ease;
	}
	.sales-arrow:hover {
	background: #c7c7c7ff; 
	}
	.sales-arrow.left {
	top: 55%;
	left: 100px;   
	}
	.sales-arrow.right {
	top: 55%;
	right: 100px;
	}
	.sales-arrow i {
	color: #333;   
	font-size: 18px;
	}

	.sales-section {
	position: relative; 
	background: #e4e4e4ff; 
	}
	.sales-section.categories{
	position: relative; 
	background: #ffffffff; 
	}

	.sales-pagination {
		display: flex;
		justify-content: center;  
		gap: 0.5rem;          
		/* margin-top: 1rem;         */
		margin-bottom: 2rem;
	}
	.sales-pagination .dot {
		width: 9px;
		height: 9px;
		border-radius: 50%;
		background-color: #afafafff;
		display: inline-block;
		transition: background 0.3s ease; 
	}
	.sales-pagination .dot.active {
		background-color: #000;
	}

	.brands-img {
	max-width: 60%;
	height: auto;
	display: block;
	margin: 50px auto 100px auto;
	}

	.choose-section {
		padding: 0; 
		background-color: white;
	}
	.choose-section .section-title {
		margin-left: 0px;
		font-family: 'OldLondon', serif; 
		font-size: 60px;                  
		font-weight: normal;               
		text-align: left;                 
		margin-bottom: 1.5rem;             
	}
	.choose-section p {
		font-weight: 600;                 
		font-size: 20px;                             
		text-align: left;             
		color: #777;                   
	}

	footer{
	margin-top: 0 !important;
	}
	body {
	background-color: white !important;
	padding-bottom: 0;
	}

	.product-image-wrapper {
	position: relative;
	display: inline-block;
	}
	.discount-badge {
	position: absolute;
	bottom: 5px;
	right: 5px;
	background-color: #dfd898;
	color: #000;
	font-size: 0.8rem;
	font-weight: bold;
	padding: 2px 6px;
	border-radius: 3px;
	}

	.price-wrapper {
	text-align: center;
	}
	.product-price,
	.product-old-price {
	display: inline-block !important; 
	vertical-align: middle;
	}
	.product-old-price {
	color: #888;           
	font-size: 0.85rem;   
	text-decoration: line-through; 
	margin-left: 6px;   
	font-weight: 600;   
	}

	.icon-cross {
	cursor: pointer !important;
	pointer-events: auto !important;   
	position: absolute !important;     
	}
	.product-item {
	position: relative;    
	}
	.product-item img,
	.product-item h3,
	.product-price {
	pointer-events: none;   
	}

	.added-message {
	position: absolute;
	bottom: 50px;
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

	white-space: nowrap; /* allow wrapping */
	text-align: center;  /* center wrapped text */
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
            <div class="hero">
				<div class="container">
					<div class="row justify-content-between">
						<div class="col-lg-5">
							<div class="intro-excerpt">
								<h1>The only place</h1>
								<p class="mb-4">to purchase the highest quality supplies for your barbershop.</p>
								<p><a href="#" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
							</div>
						</div>
						<div class="col-lg-7">
							<div class="hero-img-wrap">
								<img src="images/home-banner.png" class="img-fluid">
							</div>
						</div>
					</div>
				</div>
			</div>

            <!-- Best Sellers Section -->
			<div class="product-section">
				<div class="container">
					<div class="row">

						<div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
							<h2 class="popular-title">Popular products</h2>
							<p class="popular-subtitle">The bestsellers</p>
							<p><a href="#" class="btn">Explore</a></p>
						</div> 

						<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
							<a class="product-item" href="product.php">
								<img src="images/products/thumb_1756950792_1b6a822b.png" class="img-fluid product-thumbnail">
								<h3 class="product-title">Andis Slimline Pro Chrome Trimmer</h3>
								<strong class="product-price">$84.99</strong>

								<span class="icon-cross">
									<img src="images/cross.svg" class="img-fluid">
								</span>
							</a>
						</div> 

						<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
							<a class="product-item" href="product.php">
								<img src="images/products/thumb_1756952632_c50fa0a8.png" class="img-fluid product-thumbnail">
								<h3 class="product-title">Babyliss Black Fx One Battery System Clipper</h3>
								<strong class="product-price">$219.99</strong>

								<span class="icon-cross">
									<img src="images/cross.svg" class="img-fluid">
								</span>
							</a>
						</div>

						<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
							<a class="product-item" href="product.php">
								<img src="images/products/thumb_1757007537_f8628076.png" class="img-fluid product-thumbnail">
								<h3 class="product-title">Babyliss Light Grey LithiumFX Clipper</h3>
								<strong class="product-price">$144.99</strong>

								<span class="icon-cross">
									<img src="images/cross.svg" class="img-fluid">
								</span>
							</a>
						</div>

					</div>
				</div>
			</div>

			<!-- Sales Section -->
			 <div class="sales-section">
				<div class="sales-container">
					<div class="sales-header">
					<h2>Sales</h2>
					<img src="/barbershopSupplies/public/images/Ornament1.png" alt="Ornament">
					</div>
					<button class="sales-arrow left">
						<i class="fas fa-chevron-left"></i>
					</button>
					<button class="sales-arrow right">
						<i class="fas fa-chevron-right"></i>
					</button>

					<div class="sales-products">
						<div class="container">
							<div class="row">
								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
										<div class="product-image-wrapper">
											<img src="images/products/thumb_1756950792_1b6a822b.png" class="img-fluid product-thumbnail">
											 <div class="discount-badge">$10.99 Off</div>
										</div>
										<h3 class="product-title">Andis Slimline Pro Chrome Trimmer</h3>
										<div class="price-wrapper">
											<strong class="product-price">$84.99</strong>
											<span class="product-old-price">$199.99</span>
										</div>
										<span class="icon-cross">
											<img src="images/cross.svg" class="img-fluid">
										</span>
									</a>
								</div>

								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<div class="product-image-wrapper">
										<img src="images/products/thumb_1756952632_c50fa0a8.png" class="img-fluid product-thumbnail">
										<div class="discount-badge">$10.99 Off</div>
									</div>
									<h3 class="product-title">Babyliss Black Fx One Battery System Clipper</h3>
									<div class="price-wrapper">
										<strong class="product-price">$219.99</strong>
										<span class="product-old-price">$199.99</span>
									</div>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>

								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<div class="product-image-wrapper">
										<img src="images/products/thumb_1757007537_f8628076.png" class="img-fluid product-thumbnail">
										<div class="discount-badge">$10.99 Off</div>
									</div>
									<h3 class="product-title">Babyliss Light Grey LithiumFX Clipper</h3>
									<div class="price-wrapper">
										<strong class="product-price">$144.99</strong>
										<span class="product-old-price">$199.99</span>
									</div>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>

								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<div class="product-image-wrapper">
										<img src="images/products/thumb_1757007537_f8628076.png" class="img-fluid product-thumbnail">
										<div class="discount-badge">$10.99 Off</div>
									</div>
									<h3 class="product-title">Babyliss Light Grey LithiumFX Clipper</h3>
									<div class="price-wrapper">
										<strong class="product-price">$144.99</strong>
										<span class="product-old-price">$199.99</span>
									</div>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="sales-pagination">
						<span class="dot active"></span>
						<span class="dot"></span>
						<span class="dot"></span>
					</div>
				</div>
			</div>
			<!--  -->

			<!-- Categories Section -->
			 <div class="sales-section categories">
				<div class="sales-container">
					<div class="sales-header">
					<h2>Categories</h2>
					<img src="/barbershopSupplies/public/images/Ornament2.png" alt="Ornament">
					</div>
					<button class="sales-arrow left">
						<i class="fas fa-chevron-left"></i>
					</button>
					<button class="sales-arrow right">
						<i class="fas fa-chevron-right"></i>
					</button>

					<div class="sales-products">
						<div class="container">
							<div class="row">
								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<img src="images/products/thumb_1756950792_1b6a822b.png" class="img-fluid product-thumbnail">
									<h3 class="product-title">Clippers</h3>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>

								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<img src="images/products/thumb_1756952632_c50fa0a8.png" class="img-fluid product-thumbnail">
									<h3 class="product-title">Shavers</h3>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>

								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<img src="images/products/thumb_1757007537_f8628076.png" class="img-fluid product-thumbnail">
									<h3 class="product-title">Shaving Gel</h3>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>

								<div class="col-12 col-md-4 col-lg-3">
									<a class="product-item" href="product.php">
									<img src="images/products/thumb_1757007537_f8628076.png" class="img-fluid product-thumbnail">
									<h3 class="product-title">Trimmers</h3>
									<span class="icon-cross">
										<img src="images/cross.svg" class="img-fluid">
									</span>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="sales-pagination">
						<span class="dot active"></span>
						<span class="dot"></span>
						<span class="dot"></span>
						<span class="dot"></span>
						<span class="dot"></span>
						<span class="dot"></span>
						<span class="dot"></span>
					</div>
				</div>
			</div>

			<!-- Brands Section -->
			 <div class="sales-section">
				<div class="sales-container">
					<div class="sales-header">
						<h2>Our brands</h2>
						<img src="/barbershopSupplies/public/images/Ornament3.png" alt="Ornament">
					</div>
					<img class="brands-img" src="/barbershopSupplies/public/images/brands2.png" alt="brands">

					
				</div>
			</div>

			<!-- Start Why Choose Us Section -->
			<div class="choose-section" id="choose">
				<div class="container">
					<div class="row justify-content-between">
						<div class="col-lg-6">
							<h2 class="section-title">Why choose us</h2>
							<p>We are the leaders in providing barbershop supplies to barbers, for barbers.</p>

							<div class="row my-5">
								<div class="col-6 col-md-6">
									<div class="feature">
										<div class="icon">
											<img src="images/bag.svg" alt="Image" class="imf-fluid">
										</div>
										<h3>Easy to shop</h3>
										<p>Established in 2011, our team has been working hard to provide barbers the best service.</p>
									</div>
								</div>

								<div class="col-6 col-md-6">
									<div class="feature">
										<div class="icon">
											<img src="images/medal-icon.png" alt="Image" class="imf-fluid" width="36" height="36">
										</div>
										<h3>Customer service</h3>
										<p>A good customer service and experience is our top priority.</p>
									</div>
								</div>

								<div class="col-6 col-md-6">
									<div class="feature">
										<div class="icon">
											<img src="images/truck.svg" alt="Image" class="imf-fluid">
										</div>
										<h3>On-demand shipping</h3>
										<p>We offer on-demand delivery of your favorite and hottest products on the market. If you cannot come to us, we will come to you.</p>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>

		</main>
		<?php 
        include '../includes/footer2.php'
        ?>
		<script>
			document.querySelectorAll(
			'.product-section .product-item .icon-cross, .sales-products .product-item .icon-cross'
			).forEach(icon => {
			icon.addEventListener('click', function(event) {
				event.preventDefault();
				event.stopPropagation();

				const img = icon.querySelector('img');
				if (img) {
				img.remove();
				const check = document.createElement('i');
				check.classList.add('fas', 'fa-check');
				check.style.color = 'white';  
				check.style.fontSize = '18px';
				icon.appendChild(check);
				icon.classList.add('checkmark');

				const message = document.createElement('span');
				message.className = 'added-message';
				message.textContent = 'Added to cart!';
				icon.appendChild(message);
				void message.offsetWidth;
				message.classList.add('show');
				setTimeout(() => {
					message.classList.remove('show');
					setTimeout(() => message.remove(), 500);
				}, 2000);
				}
			});
			});
		</script>
	</body>
</html>