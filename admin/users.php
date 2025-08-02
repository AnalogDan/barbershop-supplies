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
        <?php $currentPage = 'users'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="top-bar">
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </main>
        <?php include 'includes/users-grid.php'; ?>
        <?php include 'includes/paginator.php'; ?>
        <div class="tiny-message">The users info is view-only.</div>
        <?php include 'includes/admin_footer.php'; ?>
    </body>
</html>