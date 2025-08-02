<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

function buildLinkWithParams(array $overrides = []){
    $params = $_GET;
    foreach($overrides as $key => $value){
        if ($value === null){
            unset($params[$key]);
        }else{
            $params[$key] = $value;
        }
    }
    return '?' . http_build_query($params);
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
            <div class="top-bar">
                <?php include 'includes/categ-toggle-button.php'; ?>
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <!-- Preserve category filters -->
                    <?php if (isset($_GET['main'])): ?>
                        <input type="hidden" name="main" value="<?= htmlspecialchars($_GET['main']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['subcategory'])): ?>
                        <input type="hidden" name="subcategory" value="<?= htmlspecialchars($_GET['subcategory']) ?>">
                    <?php endif; ?>
                    <!-- Preserve out-of-stock checkbox -->
                    <?php if (isset($_GET['out_of_stock'])): ?>
                        <input type="hidden" name="out_of_stock" value="1">
                    <?php endif; ?>
                </form>
            </div>
            <?php include 'includes/categories-dropdown.php'; ?>
            <label class="checkbox-wrapper">
                <input type="checkbox" name="out_of_stock" id="out_of_stock" <?= isset($_GET['out_of_stock']) ? 'checked' : '' ?>/>
                <span class="custom-checkbox">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" class="svg-inline--fa fa-check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="12" height="12">
                        <path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-12.497-12.497-12.497-32.758 0-45.255l45.255-45.255c12.497-12.497 32.758-12.497 45.255 0L192 312.69 432.702 72.988c12.497-12.497 32.758-12.497 45.255 0l45.255 45.255c12.497 12.497 12.497 32.758 0 45.255l-294.4 294.4c-12.497 12.497-32.758 12.497-45.255 0z"></path>
                    </svg>
                </span>
                Out of stock
            </label>
            <a href="products-add.php" class="btn btn-third">Add product</a>
            <?php 
                $searchQuery = $_GET['query'] ?? '';
                $mainCategoryId = $_GET['main'] ?? null;
                $subCategoryId = $_GET['subcategory'] ?? null;
                $outOfStock = $_GET['out_of_stock'] ?? null;
                include 'includes/admin-product-grid.php'; 
            ?>
            <?php include 'includes/paginator.php'; ?>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <script>
            // document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('out_of_stock').addEventListener('change', function () {
                const url = new URL(window.location);
                if (this.checked) {
                    url.searchParams.set('out_of_stock', '1');
                } else {
                    url.searchParams.delete('out_of_stock');
                }
                window.location = url.toString();
                });
            // });
        </script>
    </body>
</html>