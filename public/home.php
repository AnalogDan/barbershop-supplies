<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
?>

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

            <!-- Start Product Section -->
			<div class="product-section">
				<div class="container">
					<div class="row">

						<!-- Start Column 1 -->
						<div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
							<h2 class="popular-title">Popular products</h2>
							<p class="popular-subtitle">The bestsellers</p>
							<p><a href="#" class="btn">Explore</a></p>
						</div> 
						<!-- End Column 1 -->

						<!-- Start Column 2 -->
						<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
							<a class="product-item" href="cart.html">
								<img src="images/products/thumb_1756950792_1b6a822b.png" class="img-fluid product-thumbnail">
								<h3 class="product-title">Andis Slimline Pro Chrome Trimmer</h3>
								<strong class="product-price">$84.99</strong>

								<span class="icon-cross">
									<img src="images/cross.svg" class="img-fluid">
								</span>
							</a>
						</div> 
						<!-- End Column 2 -->

						<!-- Start Column 3 -->
						<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
							<a class="product-item" href="cart.html">
								<img src="images/products/thumb_1756952632_c50fa0a8.png" class="img-fluid product-thumbnail">
								<h3 class="product-title">Babyliss Black Fx One Battery System Clipper</h3>
								<strong class="product-price">$219.99</strong>

								<span class="icon-cross">
									<img src="images/cross.svg" class="img-fluid">
								</span>
							</a>
						</div>
						<!-- End Column 3 -->

						<!-- Start Column 4 -->
						<div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
							<a class="product-item" href="cart.html">
								<img src="images/products/thumb_1757007537_f8628076.png" class="img-fluid product-thumbnail">
								<h3 class="product-title">Babyliss Light Grey LithiumFX Clipper</h3>
								<strong class="product-price">$144.99</strong>

								<span class="icon-cross">
									<img src="images/cross.svg" class="img-fluid">
								</span>
							</a>
						</div>
						<!-- End Column 4 -->

					</div>
				</div>
			</div>
			<!-- End Product Section -->
		</main>
		<?php 
        include '../includes/footer2.php'
        ?>
	</body>
</html>