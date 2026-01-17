<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<?php
	require_once __DIR__ . '/../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'home'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="container-home mt-5 mb-5">
                <div class="text-center fw-bold">
                    <h1 class="display-4 text-black mb-3">Welcome to the admin panel!</h1>
                    <p class="lead text-muted mb-4">Use the navigation bar to manage the store</p>
                    <a href="logout.php" class="btn btn-secondary me-2">Sign out</a>
                </div>
            </div>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        
    </body>
</html>