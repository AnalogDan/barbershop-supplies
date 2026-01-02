<?php
    require_once __DIR__ . '/../includes/db.php';
	require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'cart';

	require_once __DIR__ . '/../includes/pricing.php';
	$tz = new DateTimeZone('America/Los_Angeles'); 

	//Fetch cart/product info
	$cartItems = [];
	if (!empty($_SESSION['cart_id'])) {
		$cartId = (int) $_SESSION['cart_id'];

		$stmt = $pdo->prepare("
			SELECT
				ci.product_id,
				ci.quantity,
				p.name,
				p.price,
				p.stock,
				p.cutout_image,
				p.sale_price,
				p.sale_start,
				p.sale_end
			FROM cart_items ci
			JOIN products p ON p.id = ci.product_id
			WHERE ci.cart_id = ?
		");
		$stmt->execute([$cartId]);
		$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	//Correct for stock changes
	if (!empty($cartItems)) {
		foreach ($cartItems as $item) {
			$productId = (int) $item['product_id'];
			$stock     = (int) $item['stock'];
			$quantity  = (int) $item['quantity'];
			if ($stock <= 0) {
				$stmt = $pdo->prepare("
					DELETE FROM cart_items
					WHERE cart_id = ? AND product_id = ?
				");
				$stmt->execute([$cartId, $productId]);
			} elseif ($quantity > $stock) {
				$stmt = $pdo->prepare("
					UPDATE cart_items
					SET quantity = ?
					WHERE cart_id = ? AND product_id = ?
				");
				$stmt->execute([$stock, $cartId, $productId]);
			}
		}
		$stmt = $pdo->prepare("
				SELECT
					ci.product_id,
					ci.quantity,
					p.name,
					p.price,
					p.stock,
					p.cutout_image,
					p.sale_price,
					p.sale_start,
					p.sale_end
				FROM cart_items ci
				JOIN products p ON p.id = ci.product_id
				WHERE ci.cart_id = ?
			");
			$stmt->execute([$cartId]);
			$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

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
	.item.header {
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
	position: relative;
    display: inline-flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 6px;
    overflow: visible;
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

	/*Stock message*/
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

	.cart-row { 
		display: contents; 
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
				<div class="item header">Image</div>
				<div class="item header">Product</div>
				<div class="item header">Price</div>
				<div class="item header">Quantity</div>
				<div class="item header">Total</div>
				<div class="item header">Remove</div>

				<!-- product row -->
				<?php if (empty($cartItems)): ?>
					<div class="item" style="grid-column: 1 / -1; text-align:center;">
						Your cart is empty
					</div>
				<?php else: ?>
					<?php foreach ($cartItems as $item): ?>
						<?php
							$pricing   = getProductPricing($item, $tz);
							$item['final_price']   = $pricing['final_price'];
							$item['original_price'] = $pricing['original_price'];
							$item['is_on_sale']     = $pricing['is_on_sale'];
						?>
						<div class="cart-row">
							<!-- Image -->
							<div class="item">
								<div class="item img-cell">
									<img src="<?= htmlspecialchars($item['cutout_image']) ?>" alt="">
								</div>
							</div>
							<!-- Product -->
							<div class="item">
								<?= htmlspecialchars($item['name']) ?>
							</div>
							<!-- Unit price -->
							<div class="item price"
								data-price="<?= number_format($item['final_price'], 2, '.', '') ?>">
								$<?= number_format($item['final_price'], 2) ?>
							</div>
							<!-- Quantity -->
							<div class="item">
								<div class="quantity-selector"
									data-product-id="<?= (int)$item['product_id'] ?>"
									data-max="<?= (int)$item['stock'] ?>">
									<button class="qty-btn qty-minus">−</button>
									<input type="text"
										class="qty-input"
										value="<?= (int)$item['quantity'] ?>"
										data-max="<?= (int)$item['stock'] ?>"
										readonly>
									<button class="qty-btn qty-plus">+</button>
								</div>
							</div>
							<!-- Line total -->
							<div class="item row-total">
								$<?= number_format($item['final_price'] * $item['quantity'], 2) ?>
							</div>
							<!-- Remove -->
							<div class="item">
								<i class="fa-solid fa-trash"
								data-product-id="<?= (int)$item['product_id'] ?>"></i>
							</div>
						</div>
					<?php endforeach; ?>

				<?php endif; ?>
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
						<div class="item-total subtotal">$139.99</div>
						<div class="item-total">Sales tax</div>
						<div class="item-total tax">$10.00</div>
						<div class="item-total">Shipping</div>
						<div class="item-total shipping">Free</div>
						<div class="item-total">TOTAL</div>
						<div class="item-total total">$149.99</div>
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
				const productId = selector.dataset.productId;
				const max = parseInt(selector.dataset.max, 10);
				plus.addEventListener('click', () => {
					let current = parseInt(input.value, 10);
					if (current < max) {
						const newQty = current + 1;
						input.value = newQty;
						updateRowTotal(selector);
						updateQuantity(productId, newQty, input);
					}else{
						showMessage(selector, 'No more stock');
					}
				});
				minus.addEventListener('click', () => {
					const current = parseInt(input.value);
					if (current > 1){
						const newQty = current - 1;
						input.value = newQty;
						updateRowTotal(selector);
						updateQuantity(productId, newQty, input);
					}
				});
			});

			//Update quantity function
			function updateQuantity(productId, newQty, input) {
				fetch('<?= BASE_URL ?>/../actions/cart-update-quantity.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						product_id: productId,
						quantity: newQty
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.success && data.quantity !== undefined) {
						input.value = data.quantity; 
					}
				});
			}

			//Update row total function
			function updateRowTotal(selector) {
				const row = selector.closest('.cart-row');
				const priceEl = row.querySelector('.price');
				const totalEl = row.querySelector('.row-total');
				const qty = parseInt(selector.querySelector('.qty-input').value, 10);
				const price = parseFloat(priceEl.dataset.price);

				const newTotal = (price * qty).toFixed(2);
				totalEl.textContent = `$${newTotal}`;
			}

			//Update totals function
			function updateCartTotals() {
				//Sum subtotal
				const lineTotals = document.querySelectorAll('.row-total');
				let subtotal = 0;
				lineTotals.forEach(el => {
					const val = parseFloat(el.textContent.replace('$', '')) || 0;
					subtotal += val;
				});

				// 8.25% sales tax
				const taxRate = 0.0925;
				const tax = subtotal * taxRate;

				// sChange shipping later 0.0
				const shipping = 0; 
				const total = subtotal + tax + shipping;

				// update DOM
				document.querySelector('.subtotal').textContent = `$${subtotal.toFixed(2)}`;
				document.querySelector('.tax').textContent = `$${tax.toFixed(2)}`;
				document.querySelector('.shipping').textContent = shipping === 0 ? 'Free' : `$${shipping.toFixed(2)}`;
				document.querySelector('.total').textContent = `$${total.toFixed(2)}`;
			}

			//Show message function
			function showMessage(targetElement, messageText) {
                const message = document.createElement('span');
                message.className = 'added-message';
                message.textContent = messageText;
                targetElement.appendChild(message);
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
    </body>
</html>