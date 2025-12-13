<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
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

    /*Info*/
    .info{
        /* background: lightblue; */
        font-size: 1.5rem;
        margin: 3rem auto 0rem auto;
        font-weight: 600;
        color: #6f6f6fff;
        width: auto;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .products{
        width: fit-content;
        margin-bottom: 8rem;
    }

    .item-row{
        /* background: lightcyan; */
        margin: 0.7rem 0;
        min-height: 5rem;
        gap: 1rem; 
        display: flex;
        align-items: center;  
    }
    .item-row img{
        height: 4.5rem;
        width: auto;
    }
    .item-name{
        width: 15rem;
    }

    .calc-row{
        /* background: lightblue; */
        margin: 0.7rem 0;
        min-height: 5rem;
        gap: 1rem; 
        display: flex;
        align-items: center; 
        justify-content: flex-end; 
    }
    .type{
        width: 15rem;
        display: flex;
        justify-content: right;
        margin-right: 2rem;
    }
    .money{
        min-width: auto;
        width: 8rem;
        display: flex;
        justify-content: right;
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

            <a href="my-orders.php" class="go-back">
                <i class="fa-solid fa-circle-chevron-left back-icon"></i>
            </a>
            <div class="top-container">
                <div>Order #1090</div>
                <img src="images/Ornament1.png">
            </div>

            <div class="info">
                <div>Date: May 28, 2025</div>
                <div>Status: In transit</div>
                <div>Addres: 1911 S Main St, Santa Ana, 92707</div>
                <div>Products ordered:</div>
                <div class="products">
                    <div class="item-row">
                        <span>1</span>
                        <span><img src="images/products/thumb_1756950792_1b6a822b.png" alt="productImage"></span>
                        <span class="item-name">Andis Slimline Pro Chrome Trimmer</span>
                        <span>$59.99</span>
                    </div>
                    <div class="item-row">
                        <span>3</span>
                        <span><img src="images/products/thumb_1756950792_1b6a822b.png" alt="productImage"></span>
                        <span class="item-name">Andis Slimline Pro Chrome Trimmer</span>
                        <span>$189.99</span>
                    </div>
                    <div class="calc-row">
                        <span class="type">Shipping</span>
                        <span class="money">$22.99</span>
                    </div>
                    <div class="calc-row">
                        <span class="type">Tax</span>
                        <span class="money">$9.99</span>
                    </div>
                    <div class="calc-row">
                        <span class="type">Total</span>
                        <span class="money">$262.99</span>
                    </div>
                </div>
            </div>
            
            
        </main>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            
		</script>
    </body>
</html>