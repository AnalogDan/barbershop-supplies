<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
?>
<?php require_once __DIR__ . '/../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'products'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="section-title">
				<h2>Add product</h2>
		    </div>
            <?php include 'includes/product-form.php'?>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
    </body>
</html>