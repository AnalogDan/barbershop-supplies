<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
require_once __DIR__ . '/includes/admin-auth.php';

if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<link rel="stylesheet" href="css/products-add.mobile.css">

<body>
    <?php $currentPage = 'products'; ?>
    <?php include 'includes/admin_navbar.php'; ?>
    <main>
        <div class="section-title">
            <h2>Add product</h2>
        </div>
        <?php include 'includes/product-form.php' ?>
    </main>
    <?php include 'includes/admin_footer.php'; ?>
</body>

</html>