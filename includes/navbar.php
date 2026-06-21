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

//Check if there's an active cart 
$cartItemCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(ci.quantity), 0)
        FROM carts c
        JOIN cart_items ci ON ci.cart_id = c.id
        WHERE c.user_id = ?
        AND c.status = 'active'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItemCount = (int) $stmt->fetchColumn();
} elseif (!empty($_SESSION['cart_id'])) {
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(quantity), 0)
        FROM cart_items
        WHERE cart_id = ?
    ");
    $stmt->execute([(int)$_SESSION['cart_id']]);
    $cartItemCount = (int) $stmt->fetchColumn();
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
        left: 50%;
        transform: translateX(-50%);
        background-color: #e4e4e4ff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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


    /*Navbar closeable menu*/
    .offcanvas {
        background-color: #393939;
        /* same as bg-dark */
        color: #fff;
        width: 60vw;
    }

    .offcanvas .btn-close {
        filter: invert(1);
    }

    @media (max-width: 768px) {
        .custom-navbar .navbar-brand {
            font-size: 25px;
        }
    }

    /* Cart dot when active */
    .cart-icon-wrapper {
        position: relative;
        display: inline-block;
    }

    .cart-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        background: #dfd898;
        color: #000;
        font-size: 10px;
        font-weight: bold;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 0 2px #2b2b2b;
    }
</style>

<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">

    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>">New Vision Barber Supplies</a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" id="mobileMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">
                <ul class="custom-navbar-nav navbar-nav ms-md-auto mb-2 mb-md-0">
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
                        <a class="nav-link cart-icon-wrapper" href="cart.php">
                            <img src="<?= BASE_URL ?>images/cart.png" alt="Cart" style="height:24px; width:auto;">
                            <span class="cart-badge" style="display:none;"></span>
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
    </div>

</nav>
<script src="<?= BASE_URL ?>js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.navCategoriesLink').forEach(link => {
        link.addEventListener('click', () => {
            sessionStorage.setItem('openCategoriesDropdown', '1');
        });
    });

    //Update cart badge
    window.updateCartBadge = async function() {
        try {
            const res = await fetch("<?= BASE_URL ?>actions/cart-count.php");
            const data = await res.json();
            const count = Number(data.count || 0);
            const cartLink = document.querySelector(".cart-icon-wrapper");
            if (!cartLink) return;
            const badge = cartLink.querySelector(".cart-badge");
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count > 99 ? "99+" : count;
                badge.style.display = "flex";
            } else {
                badge.textContent = "";
                badge.style.display = "none";
            }
        } catch (err) {
            console.error("Cart badge update failed:", err);
        }
    };
    document.addEventListener("DOMContentLoaded", function() {
        updateCartBadge();
    });
</script>