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
								<p><a href="" class="btn btn-secondary me-2">Shop Now</a><a href="#" class="btn btn-white-outline">Explore</a></p>
							</div>
						</div>
						<div class="col-lg-7">
							<div class="hero-img-wrap">
								<img src="images/couchhhh.png" class="img-fluid">
							</div>
						</div>
					</div>
				</div>
			</div>
            <div>
                asdsda
            </div>
		</main>
		<?php 
        include '../includes/footer.php'
        ?>
	</body>
</html>