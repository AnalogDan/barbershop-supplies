<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';

    //Only accessible once
    if (!isset($_GET['token'], $_SESSION['order_success_token']) || $_GET['token'] !== $_SESSION['order_success_token']) {
        header('Location: /barbershopSupplies/public/index.php');
        exit;
    }
    unset($_SESSION['order_success_token']);
    
    //Fetch data
    $orderId = (int)$_GET['order_id'];
    $stmt = $pdo->prepare("
        SELECT o.*, a.full_name, a.street, a.city, a.state, a.zip, a.email, a.phone
        FROM orders o
        JOIN addresses a ON o.address_id = a.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        echo "Order not found!";
        exit;
    }
    $stmt = $pdo->prepare("
        SELECT oi.*, p.cutout_image
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    main {
        background-color: #ffffffff;
    }
    .gray-container {
        background-color: #e2e2e2ff;     
        width: 90%;                 
        margin: 40px auto 0 auto;
        padding: 4rem 3rem 3rem 3rem;              
        min-height: 100vh;           
        box-shadow: 10px 10px 12px rgba(0,0,0,0.4); 
        border-radius: 10px;       
    }

    /*First block*/
    .title{
        font-size: 2rem;
        font-weight: 600;
        color: black;
    }
    .top-row{
        display: flex;
        width: 100%;
    }
    .info{
        width: 80%;
        font-size: 1.4rem;
        font-weight: 600;
        color: #676767ff;
        padding: 1.2rem 0rem 1.4rem 1.7rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .green-mark{
        margin-top: -50px;
        width: 20%;
    }
    .green-mark img{
        width: 100%;
        max-width: 200px;
        height: auto;
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.0));
    }

    .item-row{
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

    /*Second block*/
    .middle-row{
        margin: 5rem 0 0 0;
    }

    /*Last block*/
    .message{
        max-width: 35rem;
    }

    /*Totals */
    .order-summary-row {
        display: flex;
        justify-content: space-between; 
        padding: 0.3rem 0;         
        font-size: 1.2rem;
        font-weight: 600;
        color: #676767;
        width: 100%;        /* default fills parent */
        max-width: 23rem;
    }
    .order-summary-row .label {
    }
    .order-summary-row .value {
        text-align: right;
        min-width: 5rem; 
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
                            <h1>Thank you!</h1>
                            <p class="mb-4">Your order has been received.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="gray-container">
            <div class="title">
                Order summary
            </div>
            <div class="top-row">
                <div class="info">
                    <div>Order number: #<?= htmlspecialchars($order['number']) ?></div>
                    <div>Date of purchase: <?= date('m/d/Y', strtotime($order['placed_at'])) ?></div>
                    <div>Items purchased:</div>

                    <?php foreach ($items as $item): ?>
                        <div class="item-row">
                            <span>
                                <img src="<?= htmlspecialchars($item['cutout_image']) ?>" alt="">
                            </span>
                            <span class="item-name"><?= htmlspecialchars($item['product_name']) ?></span>
                            <span>$<?= number_format($item['price'], 2) ?></span>
                            <span>X<?= (int)$item['quantity'] ?></span>
                        </div>
                    <?php endforeach; ?>

                    <div class="order-summary-row">
                        <span class="label">Tax:</span>
                        <span class="value">$<?= number_format($order['sales_tax'], 2) ?></span>
                    </div>
                    <div class="order-summary-row">
                        <span class="label">Shipping:</span>
                        <span class="value">$<?= number_format($order['shipping_cost'], 2) ?></span>
                    </div>
                    <div class="order-summary-row">
                        <span class="label">Total:</span>
                        <span class="value">$<?= number_format($order['total'], 2) ?></span>
                    </div>
                    <div class="order-summary-row">
                        <span class="label">Payment method:</span>
                        <span class="value"><?= htmlspecialchars($order['payment_method']) ?></span>
                    </div>    
                </div>
                <div class="green-mark">
                    <img src="images/check.png" alt="checkmark">
                </div>
            </div>

            <div class="middle-row">
                <div class="title">
                    Shipping information
                </div>
                <div class="info">
                    <div><?= htmlspecialchars($order['full_name']) ?></div>
                    <div><?= htmlspecialchars($order['street'] . ', ' . $order['city'] . ', ' . $order['state'] . ' ' . $order['zip']) ?></div>
                    <div>UPS Ground</div>
                    <div>Estimated delivery date: <?= date('m/d/Y', strtotime($order['delivery_eta_start'])) ?> - <?= date('m/d/Y', strtotime($order['delivery_eta_end'])) ?></div>
                </div>
            </div>

            <div class="middle-row">
                <div class="title">
                    Contact information
                </div>
                <div class="info">
                    <div><?= htmlspecialchars($order['email'] ?? '') ?></div>
                    <div><?= htmlspecialchars($order['phone'] ?? '') ?></div>
                </div>
            </div>

            <div class="middle-row">
                <div class="title">
                    What's next?
                </div>
                <div class="info">
                    <div class="message">You'll receive a confirmation email shortly with your order details. When your order ships, we'll send you a tracking link.</div>
                </div>
            </div>
        </div>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>

		</script>
    </main>
	</body>
</html>