<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
    $currentPage = 'contact-us';
?>

<style>
    main {
        background-color: #ffffffff;
    }
    .gray-container {
        background-color: #e2e2e2ff;     
        width: 90%;                 
        margin: 20px auto 0 auto;
        padding: 4rem 3rem 3rem 3rem;              
        min-height: 100vh;           
        box-shadow: 10px 10px 12px rgba(0,0,0,0.4); 
        border-radius: 10px;       
    }

    /*Containers*/
    .info-container{
        width: 90%;
        margin: 20px auto 0 auto;
        height: 10rem;
        display: flex;
        align-items: center;
    }
    .info-row{
        height: 5rem;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .info1{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 33%;
        
    }
    .info2{
        height: 100%;
        width: 33%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .info3{
        height: 100%;
        width: 33%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /*Square*/
    .square{
        height: 5rem;
        width: 5rem;
        background: #2b2b2b;
        border-radius: 10px;
        display: flex;           
        justify-content: center;   
        align-items: center
    }
    .location-icon {
        color: white;
        font-size: 2.5rem;          
    }
    .text{
        margin-left: 1.2rem;
        font-size: 1rem;
        font-weight: 600;
        color: #656565ff;
        max-width: 11rem;
    }
    .phone-icon {
        color: white;
        font-size: 2.5rem;          
    }
    .insta-icon {
        height: 3rem;
        width: auto;         
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
                            <h1>Contact us</h1>
                            <p class="mb-4">Have a question? Our team is ready to help.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-container">
            <div class="info-row">
                <div class="info1">
                    <div class="square">
                        <i class="fa-solid fa-location-dot location-icon"></i>
                    </div>
                    <div class=text>
                        1911 S Main St, Santa Ana CA 92707
                    </div>
                </div>
                <div class="info2">
                    <div class="square">
                        <img class="insta-icon" src="images/instagram-gray.png">
                    </div>
                    <div class=text>
                        @new_vision_barbersupplies
                    </div>
                </div>
                <div class="info3">
                    <div class="square">
                        <i class="fa-solid fa-phone phone-icon"></i>
                    </div>
                    <div class=text>
                        657-247-4903
                    </div>
                </div>
            </div>
        </div>

        <div class="gray-container">
            
        </div>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>

		</script>
    </main>
	</body>
</html>