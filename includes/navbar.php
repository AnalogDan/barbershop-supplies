<?php
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . 'includes/db.php';

$stmt = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY id ASC
");

$categoriesById = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $categoriesById[$row['id']] = $row['name'];
}
?>

<style>
    .shop-dropdown {
        position: relative; 
    }
    .dropdown-menu-custom {
        display: none;             
        position: absolute;
        top: 100%;                   
        left: 50%;                 /* start from center of parent */
        transform: translateX(-50%);
        background-color: #e4e4e4ff;  
        border-radius: 8px;          
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        padding: 0.5rem 0;
        min-width: 120px;        
        z-index: 1000;
    }
    .shop-dropdown:hover .dropdown-menu-custom {
        display: block;
    }
    .shop-dropdown .dropdown-menu-custom li a {
        color: #000 !important;  
        text-align: center;         
        text-decoration: none;
        font-weight: 500;
    }
    .shop-dropdown .dropdown-menu-custom li a:hover {
        background-color: #d4d4d4;
        color: #000 !important;          
        border-radius: 6px;
    }
    .dropdown-menu-custom li {
        list-style: none;
    }
    .dropdown-menu-custom li a {
        display: block;
        padding: 0.5rem 1rem;
        color: #000 !important;
        text-decoration: none;
        font-weight: 500;
    }
    .dropdown-menu-custom li a:hover {
        background-color: #d4d4d4;
        border-radius: 6px;
    }

    .custom-navbar .custom-navbar-nav li.active .dropdown-menu-custom a {
    opacity: 0.5;             
    }
    .custom-navbar .custom-navbar-nav li.active .dropdown-menu-custom a:before {
    width: 0; 
    }
    .custom-navbar .custom-navbar-nav li.active .dropdown-menu-custom a:hover {
    opacity: 1;              
    }
    .custom-navbar .custom-navbar-nav li.active .dropdown-menu-custom a:hover:before {
    width: calc(100% - 16px);
    }

</style>

<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">

    <div class="container">
        <a class="navbar-brand" >New Vision Barber Supplies</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">
            <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="nav-link" style="cursor: default;">
                            Hi, <?= htmlspecialchars($_SESSION['user_first_name'] ?? 'User') ?>!
                        </span>
                    </li>
                <?php endif; ?>
                <li class="nav-item <?= ($currentPage === 'home') ? 'active' : '' ?>">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item shop-dropdown <?= ($currentPage === 'shop') ? 'active' : '' ?>">
                    <a class="nav-link" href="shop.php">Shop</a>
                    <ul class="dropdown-menu-custom">
                        <li>
                            <a href="<?= BASE_URL ?>shop.php?subcategory=50&page=1#top-bar">
                                <?= htmlspecialchars($categoriesById[50] ?? 'Category') ?>
                            </a>
                        </li>

                        <li>
                            <a href="<?= BASE_URL ?>shop.php?subcategory=51&page=1#top-bar">
                                <?= htmlspecialchars($categoriesById[51] ?? 'Category') ?>
                            </a>
                        </li>

                        <li>
                            <a href="<?= BASE_URL ?>shop.php?subcategory=52&page=1#top-bar">
                                <?= htmlspecialchars($categoriesById[52] ?? 'Category') ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>shop.php?page=1&sale=1">Sales</a>
                        </li>
                        <li class="dropdown-divider"></li>

                        <li>
                            <a href="<?= BASE_URL ?>shop.php" class="navCategoriesLink">See All Categories</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?= ($currentPage === 'contact-us') ? 'active' : '' ?>">
                    <a class="nav-link" href="contact-us.php">Contact Us</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'cart') ? 'active' : '' ?>">
                    <a class="nav-link" href="cart.php">
                        <img src="<?= BASE_URL ?>images/cart.png" alt="Cart" style="height:24px; width:auto;">
                    </a>
                </li>
                <li class="nav-item <?= ($currentPage === 'account') ? 'active' : '' ?>">
                    <a class="nav-link" href="login.php">
                        <img src="<?= BASE_URL ?>images/account.png" alt="account" style="height:24px; width:auto;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
        
</nav>
<script src="<?= BASE_URL ?>js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.navCategoriesLink').forEach(link => {
        link.addEventListener('click', () => {
            sessionStorage.setItem('openCategoriesDropdown', '1');
        });
    });
</script>