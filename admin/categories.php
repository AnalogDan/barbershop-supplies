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
        <?php $currentPage = 'categories'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="top-bar">
                <div class="category-toggle">
                    <div class="toggle-option active" data-target="main">Main categories</div>
                    <div class="toggle-option" data-target="sub">Sub categories</div>  
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
            <a href="categories-add-main.php" id="add-button" class="btn btn-third">Add</a>
            <div id="category-grid">
                <?php include 'includes/main-category-grid.php'; ?>
            </div>
            <?php 
            // include 'includes/paginator.php'; 
            ?>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <script>
            const options = document.querySelectorAll('.category-toggle .toggle-option');
            const gridContainer = document.getElementById('category-grid');
            const addButton = document.getElementById('add-button');
            options.forEach(option => {
                option.addEventListener('click', () => {
                    options.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    const selected = option.dataset.target;
                    let gridUrl = '';
                    let addUrl = '#';
                    if(selected === 'main'){
                        gridUrl = 'includes/main-category-grid.php';
                        addUrl = 'categories-add-main.php';
                    }
                    else if(selected === 'sub'){
                        gridUrl = 'includes/sub-category-grid.php';
                        addUrl = 'categories-add-sub.php';
                    }
                    fetch(gridUrl)
                        .then(res => res.text()) 
                        .then(html => {
                            gridContainer.innerHTML = html;
                            addButton.href = addUrl; 
                        });
                })
            })
        </script>
    </body>
</html>