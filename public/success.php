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
    .white-container {
        background-color: #e2e2e2ff;     
        width: 90%;                 
        margin: 70px auto 0 auto;              
        min-height: 100vh;           
        padding: 2rem;     
        box-shadow: 5px 10px 12px rgba(0,0,0,0.4); 
        border-radius: 10px;       
    }

</style>

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
    <main>
        
        <div class="white-container">
            
        </div>
    
        
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>

		</script>
    </main>
	</body>
</html>