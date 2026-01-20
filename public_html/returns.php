<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
    $currentPage = '';
?>

<style>
    main {
        background-color: #ffffffff;
    }
    .intro-excerpt h1{
        white-space: nowrap;
    }

    /*Containers*/
    .white-bar{
        background: white;
        width: 100%;
        min-height: 17rem;
        padding: 0 5rem;
        flex-direction: column;
        display: flex;
        justify-content: center;
    }
    .gray-bar{
        background: #e6e6e6ff;
        width: 100%;
        min-height: 17rem;
        padding: 0 5rem;
        flex-direction: column;
        display: flex;
        justify-content: center;
    }
    .gray-bar-tall{
        background: #e6e6e6ff;
        width: 100%;
        min-height: 17rem;
        padding: 3rem 5rem 3rem 5rem;
        flex-direction: column;
        display: flex;
        justify-content: center;
    }
    .last-gray-bar{
        background: #e6e6e6ff;
        width: 100%;
        min-height: 17rem;
        padding: 0 5rem;
        flex-direction: column;
        display: flex;
        justify-content: center;
        margin-bottom: 6rem;
    }

    /*Texts*/
    .title{
        color: black;
        font-weight: 600;
        font-size: 2rem;
        margin-top: 0rem;
    }
    .single-text{
        color: #7c7c7cff;
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 0rem;
        margin-top: 0.7rem;
        min-width: 70%;
    }
    .two-point-text{
        color: #7c7c7cff;
        font-size: 1.4rem;
        font-weight: 600;
        margin-top: 0.7rem;
        min-width: 70%;
    }
    .deeper-text{
        color: #7c7c7cff;
        font-size: 1.4rem;
        font-weight: 600;
        margin-top: 0.7rem;
        margin-left: 3rem;
        min-width: 70%;
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
                            <h1>Returns and exchanges</h1>
                            <p class="mb-4">Guidelines to ensure a smooth shoping experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- OLD POLICY -->
        <!-- <div class="white-bar">
            <div class="title">
                Product inspection
            </div>
            <div class="single-text">
                Customers are responsible for checking all products thoroughly before leaving the store or upon receiving mailed orders.
            </div>
        </div>

        <div class="gray-bar"> 
            <div class="title">    
                Warranty policy
            </div>
            <div class="two-point-text">
                -15 day warranty applies only to Wahl and Andis electronics.
            </div> 
            <div class="single-text">
                -Other electronics have their own warranty through the manufacturer. 
            </div> 
        </div>

        <div class="white-bar">
            <div class="title">
                Final sale items
            </div>
            <div class="single-text">
                All blades and shears are FINAL SALE, no returns or exchanges allowed.
            </div>
        </div>

        <div class="gray-bar-tall"> 
            <div class="title">    
                Returns and exchanges
            </div>
            <div class="two-point-text">
                -A receipt is required for all returns and exchanges.
            </div> 
            <div class="two-point-text">
                -Returns/exchanges are only accepted for manufacturer defects.
            </div>
            <div class="two-point-text">
                -In case of exchange, only the defective item will be replaced.
            </div> 
            <div class="two-point-text">
                -For all other refunds or exchanges:
            </div> 
            <div class="deeper-text">
                -The item must be unused, unopened, and undamaged.
            </div> 
            <div class="deeper-text">
                -Must include original packaging, warranty papers, and any extras.
            </div> 
        </div>

        <div class="white-bar">
            <div class="title">
                Mailed orders
            </div>
            <div class="single-text">
                If you're requesting a return or exchange on a mailed order, you are responsible for aadditional shipping costs.
            </div>
        </div>

        <div class="last-gray-bar">
            <div class="title">
                Need to start a return?
            </div>
            <div class="single-text">
                Email us at newvisionbarbersupplies@gmail.com with your order number and the reason for your return.
            </div>
        </div> -->

        <div class="white-bar">
    <div class="title">
        Product inspection
    </div>
    <div class="single-text">
        All items are carefully packaged, handled, and inspected before shipment to ensure they are new, undamaged, and working properly. Customers are responsible for inspecting all products immediately upon delivery. If a package appears damaged, please refuse the delivery and ask the carrier to return it to New Vision Barber Supply.
    </div>
</div>

<div class="gray-bar"> 
    <div class="title">    
        Reporting issues
    </div>
    <div class="two-point-text">
        -You have 7 days from the delivery date to report any defects, missing items, or issues.
    </div> 
    <div class="single-text">
        -After this period, the manufacturer’s warranty will apply if available.
    </div> 
</div>

<div class="white-bar">
    <div class="title">
        Returns and exchanges
    </div>
    <div class="single-text">
        Unwanted items may be refunded, exchanged, or credited only if they are unused, unopened, and in original condition. Shipping charges are non-refundable. Customers are responsible for all return shipping costs.
    </div>
</div>

<div class="gray-bar-tall"> 
    <div class="title">    
        Return conditions
    </div>
    <div class="two-point-text">
        -A valid receipt or order number is required for all returns and exchanges.
    </div> 
    <div class="two-point-text">
        -Returns and exchanges are accepted only for manufacturer defects.
    </div>
    <div class="two-point-text">
        -In the case of an exchange, only the defective item will be replaced.
    </div> 
    <div class="two-point-text">
        -Returned items must be properly packaged to prevent damage.
    </div> 
    <div class="deeper-text">
        -Items returned damaged due to poor packaging will not be accepted.
    </div> 
</div>

<div class="white-bar">
    <div class="title">
        Non-refundable items
    </div>
    <div class="single-text">
        The following items are FINAL SALE and are not eligible for returns or exchanges: clipper, trimmer, and shaver blades; liquids; scissors and shears; straight razors; razor blades; chemicals; aerosols; gels; pomades; shaving gels and liquids.
    </div>
</div>

<div class="gray-bar">
    <div class="title">
        Shipping responsibility
    </div>
    <div class="single-text">
        New Vision Barber Supply is not responsible for stolen, lost, or misdelivered packages once they are in the possession of the shipping carrier. If an issue occurs, please contact the carrier immediately. You may also email us to request your tracking number.
    </div>
</div>

<div class="white-bar">
    <div class="title">
        International orders
    </div>
    <div class="single-text">
        All orders shipped outside of the United States are FINAL SALE. Delivery times may vary, and delays caused by the shipping carrier are outside our control. If a package arrives damaged, please refuse delivery and contact the carrier immediately.
    </div>
</div>

<div class="last-gray-bar">
    <div class="title">
        Need to start a return?
    </div>
    <div class="single-text">
        To report any issues or request a return, you must contact us by email. Email is the only accepted method for reporting problems. Please email newvisionbarbersupplies@gmail.com and include your order number and a description of the issue.
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