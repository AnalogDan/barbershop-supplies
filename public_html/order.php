<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = '';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $userId = $_SESSION['user_id'] ?? null;

    
    $orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($orderId <= 0) {
        die('Invalid order ID.');
    }

    // Fetch order and user address
    $stmt = $pdo->prepare("
        SELECT o.*, a.full_name, a.street, a.city, a.state, a.zip, a.email, a.phone
        FROM orders o
        LEFT JOIN addresses a ON o.address_id = a.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        die('Order not found.');
    }
    //Fetch order items
    $stmtItems = $pdo->prepare("
        SELECT 
            oi.product_id,
            oi.quantity, 
            oi.price, 
            oi.product_name, 
            p.cutout_image
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmtItems->execute([$orderId]);
    $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
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
        /* font-family: 'OldLondon', serif; */
        font-size: 2.5rem;
        color: #414141ff;
        weight: 800;
    }
    .top-container img{
        height: 5rem;
        margin-top: 1rem;
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

    /*Image */
    .item-row span:nth-child(2) {
        width: 65px;       
        height: 65px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;       
    }
    .item-row span:nth-child(2) img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;  
        display: block;
    }
    /*Right align product price*/
    .item-row span:nth-child(4) {
        margin-left: auto;
        text-align: right;
        white-space: nowrap;
    }
    /*Item link */
    .item-name a {
        color: inherit;
        text-decoration: none;
    }
    .item-name a:hover {
        text-decoration: underline;
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
                <div>Order #<?= htmlspecialchars($order['number']) ?></div>
                <!-- <img src="images/Ornament1.png"> -->
            </div>

            <div class="info">
                <div>Date: <?= date('M d, Y', strtotime($order['placed_at'])) ?></div>
                <div>Status: <?= ucfirst($order['status']) ?></div>
                <div>Payment method: <?= htmlspecialchars($order['payment_method']) ?></div>
                <div>Address: <?= htmlspecialchars($order['street'] . ', ' . $order['city'] . ', ' . $order['state'] . ', ' . $order['zip']) ?></div>
                <div> 
                    Delivery estimated time:
                    <?= htmlspecialchars($order['delivery_eta_start'] ?? '—') ?>
                    --
                    <?= htmlspecialchars($order['delivery_eta_end'] ?? '—') ?>
                </div>    
                <div>Email: <?= htmlspecialchars($order['email']) ?></div>
                <div>Phone: <?= htmlspecialchars($order['phone']) ?></div>
                <div>Tracking number: 1234567</div> <!-- Dummy for now -->
                <div>Products ordered:</div>
                <div class="products">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="item-row">
                            <span><?= (int)$item['quantity'] ?></span>

                            <span>
                                <img
                                    src="<?= htmlspecialchars($item['cutout_image'] ?: 'images/products/gallery_123456789.jpg') ?>"
                                    alt="<?= htmlspecialchars($item['product_name']) ?>"
                                >
                            </span>

                            <span class="item-name">
                                <a href="product.php?id=<?= (int)$item['product_id'] ?>">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </a>
                            </span>

                            <span>
                                $<?= number_format($item['price'], 2) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                    <div class="calc-row">
                        <span class="type">Subtotal</span>
                        <span class="money">$<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <div class="calc-row">
                        <span class="type">Shipping</span>
                        <span class="money">$<?= number_format($order['shipping_cost'], 2) ?></span>
                    </div>
                    <div class="calc-row">
                        <span class="type">Tax</span>
                        <span class="money">$<?= number_format($order['sales_tax'], 2) ?></span>
                    </div>
                    <div class="calc-row">
                        <span class="type">Total</span>
                        <span class="money">$<?= number_format($order['total'], 2) ?></span>
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