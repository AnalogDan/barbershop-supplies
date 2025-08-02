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
        <?php $currentPage = 'giftcards'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="top-bar">
                <div class="category-toggle order-version">
                    <div class="toggle-option active" data-target="all">All</div>
                    <div class="toggle-option" data-target="unused">Unused</div>
                    <div class="toggle-option" data-target="used">Used</div>
                </div>
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <a href="giftcards-add.php" id="add-button" class="btn btn-third">Add</a>
            <?php include 'includes/giftcards-grid.php'; ?>
        </main>
        <?php include 'includes/paginator.php'; ?>
        <?php include 'includes/admin_footer.php'; ?>
        <script>
            const options = document.querySelectorAll('.category-toggle .toggle-option');
            options.forEach(option => {
                option.addEventListener('click', () => {
                    options.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    const selected = option.dataset.target;
                    console.log("Selected:", selected);
                })
            })
        </script>
    </body>
</html>