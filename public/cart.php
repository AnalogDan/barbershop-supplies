<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
	$currentPage = 'cart';
?>

<style>
	/*Grid styling*/
	.grid{
		color: #696969ff;
		display: grid;
		grid-template-columns: repeat(6, 1fr);
		width: 80%;
		margin: 120px auto 0 auto;
	}
	 .item {
		padding: 20px;
		text-align: center;
		border-bottom: 2px solid #b9b9b9ff;
		font-weight: 600;
		display: flex;
		align-items: center;     
		justify-content: center;
	}
	.item i {
	cursor: pointer;
	font-size: 20px;
	transition: color 0.2s ease, transform 0.2s ease;
	}
	.item i:hover {
	color: black;
	transform: scale(1.15);
	}

	/*Grid title row*/
	.grid .item:nth-child(-n+6) {
		font-size: 17px;
		border-bottom: 3px solid black;
		font-weight: 550;
		color: black;
	}

	/*grid contents*/
	.img-cell {
	border: 0px solid #ff0000ff  !important;
	width: 80px;
	height: 80px;
	padding: 0;
	display: flex;
	margin: auto;
	justify-content: center;
	align-items: center;
	}
	.img-cell img {
	width: 100%;
	height: 100%;
	object-fit: contain; 
	}
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
	.item > .quantity-selector {
    margin: 0 auto;
    width: fit-content;
	}
    .qty-btn:hover {
    background: #e0e0e0;
    }
    .qty-input {
    width: 50px;
    text-align: center;
    border: none;
    font-size: 16px;
    outline: none;
    background: white;
    }

	/*Bottom section*/
	.bottom-section {
	width: 80%; 
	margin: 50px auto 170px auto; 
	}
	.left {
	width: 65%;              
    float: left;           
    padding: 1rem;          
    box-sizing: border-box; 
	font-size: 1.7rem;
	color: black;
	font-weight: 600;
	}
	.left .subtitle {
	color: #696969ff;
	margin-top: 10px;
	margin-bottom: 5px;
	font-size: 1rem;
	font-weight: 550;	
	}
	#product-search-form {
		width: 320px;    
		margin-left: 0 !important;     
		margin-right: auto;
		padding: 0;
	}
	.btn {
		margin-top: -25px;
		border-radius: 10px !important;  
	}
	.inline-wrapper {
		display: inline-flex;
		align-items: center;
		gap: 10px; 
		justify-content: flex-start;
	}

	.right {
    width: 35%;              
    float: right;           
    padding: 1rem;          
    box-sizing: border-box;  
    }
	.grid-total{
		color: #696969ff;
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		width: 100%;
	}
	 .item-total{
		padding: 10px;
		text-align: left;
		border-bottom: 2px solid #b9b9b9ff;
		font-weight: 600;
		display: flex;
		align-items: left;     
		justify-content: left;
	}
	.grid-total .item-total:nth-child(-n+2) {
		font-size: 17px;
		border-bottom: 3px solid black;
		font-weight: 550;
		color: black;
	}
	.check-btn{
		margin-top: 30px;
		float: right;
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
								<h1>Cart</h1>
								<p class="mb-4">Review your items before proceeding to checkout.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="grid">
				<div class="item">Image</div>
				<div class="item">Product</div>
				<div class="item">Price</div>
				<div class="item">Quantity</div>
				<div class="item">Total</div>
				<div class="item">Remove</div>

				<!-- frontend row -->
				<div class="item">
					<div class="item img-cell">
						<img src="images/products/thumb_1756950792_1b6a822b.png" alt="Image">
					</div>
				</div>
				<div class="item">Andis Slimline Pro Chrome Trimmer</div>
				<div class="item">$84.99</div>
				<div class="item">
					<div class="quantity-selector">
						<button class="qty-btn qty-minus">−</button>
						<input type="text" class="qty-input" value="1" readonly>
						<button class="qty-btn qty-plus">+</button>
					</div>
				</div>
				<div class="item">$84.99</div>
				<div class="item"><i class="fa-solid fa-trash"></i></div>
				<!--loop frontend row -->
				<?php for ($i = 0; $i < 3; $i++): ?>
					<div class="item">
						<div class="item img-cell">
							<img src="images/products/thumb_1756950792_1b6a822b.png" alt="Image">
						</div>
					</div>

					<div class="item">Andis Slimline Pro Chrome Trimmer</div>

					<div class="item">$84.99</div>

					<div class="item">
						<div class="quantity-selector">
							<button class="qty-btn qty-minus">−</button>
							<input type="text" class="qty-input" value="1" readonly>
							<button class="qty-btn qty-plus">+</button>
						</div>
					</div>

					<div class="item">$84.99</div>

					<div class="item"><i class="fa-solid fa-trash"></i></div>
				<?php endfor; ?>
			</div>

			<div class="bottom-section">
				<div class="left">
					<div>Gift Card</div>
					<div class="subtitle">Enter gift card code if you have one.</div>
					<div class="inline-wrapper">
						<form id="product-search-form" class="search-bar" action="#" method="GET">
							<div class="search-wrapper">
								<input type="text" name="" id="" placeholder="XXXXXX-0000" value=""/>
							</div>
						</form>
						<a href="#" class="btn">Apply</a>
					</div>
				</div>
				<div class="right">
					<div class="grid-total">
						<div class="item-total">CART TOTALS</div>
						<div class="item-total"></div>
						<div class="item-total">Subtotal</div>
						<div class="item-total">$139.99</div>
						<div class="item-total">Sales tax</div>
						<div class="item-total">$10.00</div>
						<div class="item-total">Shipping</div>
						<div class="item-total">Free</div>
						<div class="item-total">TOTAL</div>
						<div class="item-total">$149.99</div>
					</div>
					<a href="checkout.php" class="btn check-btn">Proceed to checkout</a>
			</div>


        </main>
        <?php 
        include '../includes/footer.php'
        ?>
        <script>
			//Quantity selector
            document.querySelectorAll('.quantity-selector').forEach(selector => {
				const minus = selector.querySelector('.qty-minus');
				const plus = selector.querySelector('.qty-plus');
				const input = selector.querySelector('.qty-input');
				plus.addEventListener('click', () => {
					input.value = parseInt(input.value) + 1;
				});
				minus.addEventListener('click', () => {
					const current = parseInt(input.value);
					if (current > 1) input.value = current - 1;
				});
			});
		</script>
    </body>
</html>