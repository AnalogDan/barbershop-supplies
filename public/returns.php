<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
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
    }
    .gray-bar{
        background: #e6e6e6ff;
        width: 100%;
        min-height: 17rem;
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

        <div class="white-bar">
            asadita
        </div>
        <div class="gray-bar"> 
            asswhole   
        </div>

        <?php 
        include '../includes/footer2.php'
        ?>
        <script>

		</script>
    </main>
	</body>
</html>