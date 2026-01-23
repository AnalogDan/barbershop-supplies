<style>
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(6, auto);  
        gap: 1rem;                            
        width: 100%;
        margin: 0 auto;
		padding: 0 80px;
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
	width: 100%;
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
	bottom: 50px;        
	left: 50%;
	transform: translateX(-50%);
	white-space: nowrap;    
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
	.sold-badge{
	position: absolute;
	bottom: 45px;
	right: 5px;
	background-color: #ffb5aaff;
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
	bottom: -60px;
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

	.no-products {
		grid-column: 1 / -1;     
		text-align: center;
		font-size: 1.4rem;
		font-weight: 600;
		color: #626262ff;
		padding: 40px 0;
	}
</style>

<div class="products-grid">

	<?php if (empty($products)): ?>
		<div class="no-products">
			No products found.
		</div>
	<?php else: ?>
		<?php foreach ($products as $p): ?>
			<?php
			$tz = new DateTimeZone('America/Los_Angeles');
			$currentTime = new DateTime('now', $tz);

			$isOnSale = false;

			if (!empty($p['sale_price'])) {
				$tz = new DateTimeZone('America/Los_Angeles');
				$currentTime = new DateTime('now', $tz);
				$saleStart = !empty($p['sale_start']) ? new DateTime($p['sale_start'], $tz) : null;
				$saleEnd   = !empty($p['sale_end'])   ? new DateTime($p['sale_end'], $tz)   : null;
				if (
					($saleStart === null && $saleEnd === null) ||
					($saleStart !== null && $saleEnd === null && $currentTime >= $saleStart) ||
					($saleStart === null && $saleEnd !== null && $currentTime <= $saleEnd) ||
					($saleStart !== null && $saleEnd !== null && $currentTime >= $saleStart && $currentTime <= $saleEnd)
				) {
					$isOnSale = true;
				}
			}

			$isSoldOut = false;
			if ((int)$p['stock'] === 0) {
				$isSoldOut = true;
			}
			?>

			<div class="sales-products">
				<a class="product-item" href="<?= BASE_URL ?>product.php?id=<?php echo $p['id']; ?>" data-product-id="<?php echo $p['id']; ?>">
					<div class="product-image-wrapper">
						<img src="<?php echo htmlspecialchars($p['cutout_image']); ?>" class="img-fluid product-thumbnail">

						<?php if ($isOnSale): ?>
							<?php
							$discountPercent = 0;

							if ($p['price'] > 0 && $p['sale_price'] < $p['price']) {
								$discountPercent = round(
									(($p['price'] - $p['sale_price']) / $p['price']) * 100
								);
							}
							?>
							<div class="discount-badge">
								<?= $discountPercent ?>% Off
							</div>
						<?php endif; ?>

						<?php if ($isSoldOut): ?>
							<div class="sold-badge">
								Sold out
							</div>
						<?php endif; ?>
					</div>

					<h3 class="product-title"><?php echo htmlspecialchars($p['name']); ?></h3>
					<div class="price-wrapper">
						<?php if ($isOnSale): ?>
							<strong class="product-price">$<?php echo number_format($p['sale_price'], 2); ?></strong>
							<span class="product-old-price">$<?php echo number_format($p['price'], 2); ?></span>
						<?php else: ?>
							<strong class="product-price">$<?php echo number_format($p['price'], 2); ?></strong>
						<?php endif; ?>
					</div>

					<span class="icon-cross">
						<img src="images/cross.svg" class="img-fluid">
					</span>
				</a>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<script>
	//Add to cart
	document.querySelectorAll(
		'.product-section .product-item .icon-cross, .sales-products .product-item .icon-cross'
	).forEach(icon => {
		icon.addEventListener('click', function(event) {
			event.preventDefault();
			event.stopPropagation();

			const productId = icon.closest('.product-item')?.dataset.productId;
			if (!productId) return;

			addToCart(productId, 1, () => {
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
	});

	//Function to add to cart
	function addToCart(productId, quantity, onSuccess) {
		fetch('<?= BASE_URL ?>actions/cart-add.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				product_id: productId,
				quantity: quantity
			})
		})
		.then(res => res.json())
		.then(data => {
			console.log('Add to cart response:', data);
			if (!data.success) {
				console.error(data.message);
				return;
			}

			if (typeof onSuccess === 'function') {
				onSuccess(data);
			}
		})
		.catch(err => console.error('Fetch error:', err));
	}

	//Product hover effect on mobile
	function enableMobileHover() {
	if (window.innerWidth > 768) return;
		document.querySelectorAll('.product-item').forEach(item => {
			item.addEventListener('click', function (e) {
			if (e.target.closest('.icon-cross')) return;

			if (!this.classList.contains('active')) {
				e.preventDefault();
				document.querySelectorAll('.product-item.active')
				.forEach(i => i.classList.remove('active'));
				this.classList.add('active');
			}
			});
		});
	}
	enableMobileHover();
</script>

