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
                    <?php $grid = $_GET['grid'] ?? 'main'; ?>
                    <div class="toggle-option <?= ($grid === 'main') ? 'active' : '' ?>" data-target="main">Main categories</div>
                    <div class="toggle-option <?= ($grid === 'sub') ? 'active' : '' ?>" data-target="sub">Sub categories</div> 
                </div>
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <input type="hidden" name="grid" id="grid-type" placeholder="Search name..." value="<?= htmlspecialchars($_GET['grid'] ?? 'main') ?>">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query" placeholder="Search name..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <a href="categories-add-main.php" id="add-button" class="btn btn-third">Add</a>
            <div id="category-grid">
                <?php 
                    $grid = $_GET['grid'] ?? 'main';
                    if($grid === 'sub'){
                        include 'includes/sub-category-grid.php';
                    } else {
                        include 'includes/main-category-grid.php';
                    }
                ?>
            </div>
            <?php 
            ?>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <?php include 'includes/main-category-grid-script.php'; ?>
        <?php include 'includes/sub-category-grid-script.php'; ?>
        
        <?php include 'includes/modals.php'; ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const grid = '<?= $grid ?>';
                if (!window.location.search.includes('grid=')) {
                    const newUrl = window.location.pathname + '?grid=' + grid;
                    window.history.replaceState(null, '', newUrl);
                }
                const addButton = document.getElementById('add-button');
                const currentGrid = getCurrentGrid(); 
                if (currentGrid === 'sub') {
                    addEventSubName();
                    addButton.href = 'categories-add-sub.php';    
                } else {
                    addEventMainName();
                    addEventMainDelete();
                    addButton.href = 'categories-add-main.php';  
                }
            });
            function getCurrentGrid() {
                const params = new URLSearchParams(window.location.search);
                return params.get('grid') || 'main';
            }

            window.addEventListener('popstate', () => {
                const currentGrid = getCurrentGrid();
                handleGridChange(currentGrid);
            });
            function handleGridChange(grid) {
                if (grid === 'main') {
                    document.querySelectorAll('.product-grid .name[contenteditable="true"]').forEach(div => {
                        div.removeEventListener('blur', handleBlurSub);
                    });
                    document.querySelectorAll('.parent-category').forEach(select => {
                        select.removeEventListener('change', handleParentSub);
                    });
                    document.querySelectorAll('.delete-icon').forEach(btn => {
                        btn.removeEventListener('click', handleDeleteSub);
                    });
                } else if (grid === 'sub') {
                    document.querySelectorAll('.name').forEach(el => {
                        el.removeEventListener('blur', handleNameBlur);
                    });
                    document.querySelectorAll('.delete-icon').forEach(el => {
                        el.removeEventListener('click', handleDeleteMain);
                    });
                }
            }


            const options = document.querySelectorAll('.category-toggle .toggle-option');
            const gridContainer = document.getElementById('category-grid');
            const addButton = document.getElementById('add-button');
            options.forEach(option => {
                option.addEventListener('click', () => {
                    const selected = option.dataset.target;
                    document.getElementById('search-query').value = '';
                    const searchValue = document.getElementById('search-query').value; 
                    const newUrl = window.location.pathname + '?grid=' + selected;
                    window.history.replaceState(null, '', newUrl);
                    options.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    document.getElementById('grid-type').value = selected;
                    let gridUrl = '';
                    let addUrl = '#';
                    if(selected === 'main'){
                        gridUrl = 'includes/main-category-grid.php';
                        addUrl = 'categories-add-main.php';

                        fetch(gridUrl + '?query=' + encodeURIComponent(searchValue))
                        .then(res => res.text()) 
                        .then(html => {
                            gridContainer.innerHTML = html;
                            addButton.href = addUrl; 
                            addEventMainName();
                            addEventMainDelete();
                        });

                    }
                    else if(selected === 'sub'){
                        gridUrl = 'includes/sub-category-grid.php';
                        addUrl = 'categories-add-sub.php';

                        fetch(gridUrl + '?query=' + encodeURIComponent(searchValue))
                        .then(res => res.text()) 
                        .then(html => {
                            gridContainer.innerHTML = html;
                            addButton.href = addUrl; 
                            addEventSubName();
                        });
                    }
                });
            });

        </script>
    </body>
</html>