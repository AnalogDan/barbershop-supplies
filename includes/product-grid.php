<style>
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(6, auto);  
        gap: 2rem;                            
        width: 100%;
        margin: 0 auto;
    }
    .product-cell {
        
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

	white-space: nowrap; /* allow wrapping */
	text-align: center;  /* center wrapped text */
	}
	.added-message.show {
	opacity: 1;
	transform: translateX(-50%) translateY(-10px);
	}
</style>

<div class="products-grid">
    <?php
    $rows = 6;
    $cols = 4;

    for ($i = 0; $i < $rows * $cols; $i++): ?>
        <div class="product-cell">
            <div class="sales-products"></div>
                <a class="product-item" href="cart.html">
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
        </div>
    <?php endfor; ?>
</div>

<div class="sales-products">
    <a class="product-item" href="cart.html">
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