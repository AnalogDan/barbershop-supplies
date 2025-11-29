<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
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
                    <div>Order number: #1081</div>
                    <div>Date of purchase: 6/21/2025</div>
                    <div>Items purchased:</div>
                    <div class="item-row">
                        <span>1</span>
                        <span><img src="images/products/thumb_1756950792_1b6a822b.png"></span>
                        <span class="item-name">Andis Slimline Pro Chrome Trimmer</span>
                        <span>$59.99</span>
                    </div>
                    <div class="item-row">
                        <span>3</span>
                        <span><img src="images/products/thumb_1756951211_dfdb4e50.png"></span>
                        <span class="item-name">Trimmer OMG Super Trimmer Pro</span>
                        <span>$159.98</span>
                    </div>
                    <div>Total (shipment and tax included): $219.99</div>
                    <div>Payment method: Visa ****4242</div>
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
                    <div>John Doe</div>
                    <div>1911 S Main St, Santa Ana, CA 92707</div>
                    <div>UPS Ground</div>
                    <div>Estimated delivery date: June 25-29</div>
                </div>
            </div>

            <div class="middle-row">
                <div class="title">
                    Contact information
                </div>
                <div class="info">
                    <div>johndoes_things@irs.gov</div>
                    <div>3921077756</div>
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