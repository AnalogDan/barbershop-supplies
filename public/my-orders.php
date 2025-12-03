<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
	$currentPage = '';
?>

<style>
    /*Top row*/
    .go-back{
        color: #6f6f6fff;
        height: 3.5rem;
        width: 10%;
        display: flex;
        align-items: center;
        justify-content: flex-end; 
        text-decoration: none;
    }
    .back-icon{
        margin-top: 9rem;
        font-size: 3.5rem;
        width: 3.5rem;
        transition: color 0.2s ease, transform 0.2s ease;
        cursor: pointer;
	}
	.back-icon:hover {
        color: black;
	}
    
    .top-container{
        height: 10rem;
        width: 80%;
        margin: 0 auto 0 auto;
        display: flex;
        flex-direction: column;     
        justify-content: center;    
        align-items: center;   
        color: black;
        font-family: 'OldLondon', serif;
        font-size: 3rem;
    }
    .top-container img{
        height: 5rem;
    }

    /*Orders*/
    .orders-list{
        height: auto;
        width: auto;
        /* background-color: lightblue; */
        margin: 2rem auto 3rem auto;
        color: #6f6f6fff;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .view{
        color: #6f6f6fff;
        text-decoration: underline;
        cursor: pointer;
    }
    .order-row{
        justify-content: center;   
        display: flex;
        margin-bottom: 1.2rem;
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
								<h1>My profile</h1>
								<p class="mb-4">Manage your account details, orders and favorites.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

            <a href="my-profile.php" class="go-back">
                <i class="fa-solid fa-circle-chevron-left back-icon"></i>
</a>
            <div class="top-container">
                <div>All orders</div>
                <img src="images/Ornament1.png">
            </div>

            <div class="orders-list">
                <div class="order-row">
                    <div>
                        Order #1090 - Apr 22, 2025 - $89.99 - Delivered -
                    </div>
                    <a href="order.php" class="view">View</a>
                </div>
                <div class="order-row">
                    <div>
                        Order #1991 - Apr 23, 2025 - $69.99 - In transit -
                    </div>
                    <a href="order.php" class="view">View</a>
                </div>
                <div class="order-row">
                    <div>
                        Order #1093 - Dec 22, 2025 - $89.99 - Delivered -
                    </div>
                    <a href="order.php" class="view">View</a>
                </div>
                <div class="order-row">
                    <div>
                        Order #1090 - Jan 22, 2025 - $109.99 - Delivered -
                    </div>
                    <a href="order.php" class="view">View</a>
                </div>
                
            </div>

            <?php 
            include __DIR__ . '/../includes/paginator.php'; 
            ?>
            
        </main>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            
		</script>
    </body>
</html>