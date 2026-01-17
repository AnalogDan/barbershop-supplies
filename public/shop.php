<?php
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'shop';
    
    //Favorites if not logged
    $favoritesActive = isset($_GET['favorites']) && (int)$_GET['favorites'] === 1;
    if ($favoritesActive && empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $userId = $_SESSION['user_id'] ?? null;

    //Variables for sort type
    $sort = $_GET['sort'] ?? 'default';
    $orderBy = match ($sort) {
        'price_low'  => "CASE
                            WHEN sale_price IS NOT NULL
                                AND (sale_start IS NULL OR sale_start <= NOW())
                                AND (sale_end IS NULL OR sale_end >= NOW())
                            THEN sale_price
                            ELSE price
                        END ASC",
        'price_high' => "CASE
                            WHEN sale_price IS NOT NULL
                                AND (sale_start IS NULL OR sale_start <= NOW())
                                AND (sale_end IS NULL OR sale_end >= NOW())
                            THEN sale_price
                            ELSE price
                        END DESC",
        default      => 'id ASC'
    };

    //Variables for pagination
    $productsPerPage = 28;
    $currentPageNum = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; 
    $offset = ($currentPageNum - 1) * $productsPerPage;

    //Select the correct id from url
    $mainCategoryId = isset($_GET['main']) ? (int) $_GET['main'] : null;
    $subCategoryId  = isset($_GET['subcategory']) ? (int) $_GET['subcategory'] : null;
    $filterType = null;
    $filterValue = null;
    if ($subCategoryId) {
        $filterType = 'subcategory';
        $filterValue = $subCategoryId;
    } else if ($mainCategoryId) {
        $filterType = 'main';
        $filterValue = $mainCategoryId;
    }

    //Grab $categoryName
    $categoryName = 'All'; 
    if ($subCategoryId) {
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$subCategoryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $categoryName = $row['name'];

    } else if ($mainCategoryId) {
        $stmt = $pdo->prepare("SELECT name FROM main_categories WHERE id = ?");
        $stmt->execute([$mainCategoryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $categoryName = $row['name'];
    }

    //Query variable
    $searchQuery = isset($_GET['query']) && trim($_GET['query']) !== '' ? trim($_GET['query']) : null;

    //Gather selected products info
    if ($subCategoryId) {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image, stock
                FROM products
                WHERE category_id = ?
                AND name LIKE ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $subCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(3, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image, stock
                FROM products
                WHERE category_id = ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $subCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if ($mainCategoryId) {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.sale_start, p.sale_end, p.cutout_image, p.stock
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.main_category_id = ?
                AND p.name LIKE ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $mainCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(3, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.sale_start, p.sale_end, p.cutout_image, p.stock
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.main_category_id = ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $mainCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if (isset($_GET['favorites']) && (int)$_GET['favorites'] === 1) {
        if (empty($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        $userId = $_SESSION['user_id'];

        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.sale_start, p.sale_end, p.cutout_image, p.stock
                FROM products p
                INNER JOIN favorites f ON f.product_id = p.id
                WHERE f.user_id = ?
                AND p.name LIKE ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(3, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.sale_start, p.sale_end, p.cutout_image, p.stock
                FROM products p
                INNER JOIN favorites f ON f.product_id = p.id
                WHERE f.user_id = ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }else if (isset($_GET['sale']) && (int)$_GET['sale'] === 1){
        require_once __DIR__ . '/../includes/pricing.php';
        $tz = new DateTimeZone('America/Los_Angeles');

        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image, stock
                FROM products
                WHERE sale_price IS NOT NULL
                AND name LIKE ?
                ORDER BY $orderBy
            ");
            $stmt->bindValue(1, "%{$searchQuery}%", PDO::PARAM_STR);
        } else {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image, stock
                FROM products
                WHERE sale_price IS NOT NULL
                ORDER BY $orderBy
            ");
        }
        $stmt->execute();
        $allPotentialSaleProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $allActiveSaleProducts = array_values(array_filter(
            $allPotentialSaleProducts,
            fn($p) => isProductOnSale($p, $tz)
        ));

        $products = array_slice($allActiveSaleProducts, $offset, $productsPerPage);

        $totalProducts = count($allActiveSaleProducts);
        $totalPages = (int) ceil($totalProducts / $productsPerPage);
    } else {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image, stock
                FROM products
                WHERE name LIKE ?
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image, stock
                FROM products
                ORDER BY $orderBy
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    //Count total products and pages
    if ($subCategoryId) {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM products
                WHERE category_id = ?
                AND name LIKE ?
            ");
            $stmt->bindValue(1, $subCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
        } else {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM products
                WHERE category_id = ?
            ");
            $stmt->bindValue(1, $subCategoryId, PDO::PARAM_INT);
        }
        $stmt->execute();
    } else if ($mainCategoryId) {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.main_category_id = ?
                AND p.name LIKE ?
            ");
            $stmt->bindValue(1, $mainCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
        } else {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.main_category_id = ?
            ");
            $stmt->bindValue(1, $mainCategoryId, PDO::PARAM_INT);
        }
        $stmt->execute();
   } else if (isset($_GET['favorites']) && (int)$_GET['favorites'] === 1) {
        // --- Favorites count branch ---
        if (empty($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        $userId = $_SESSION['user_id'];

        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM favorites f
                INNER JOIN products p ON f.product_id = p.id
                WHERE f.user_id = ?
                AND p.name LIKE ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
        } else {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM favorites f
                INNER JOIN products p ON f.product_id = p.id
                WHERE f.user_id = ?
            ");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        }
        $stmt->execute();
    } else {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM products
                WHERE name LIKE ?
            ");
            $stmt->bindValue(1, "%{$searchQuery}%", PDO::PARAM_STR);
        } else {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM products
            ");
        }
        $stmt->execute();
    }
    if (!isset($_GET['sale']) || (int)$_GET['sale'] !== 1) {
        $totalProducts = (int) $stmt->fetchColumn();
        $totalPages = (int) ceil($totalProducts / $productsPerPage);
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

<style>
    .sales-top-bar {
    display: flex;
    width: 70%;
    }
    .sales-header {
	padding: 2rem 0 0 0;
	margin-bottom: 10px;
	text-align: center;
    flex: 1;      
	}
	.sales-header h2 {
	font-family: 'OldLondon', serif;  
	font-size: 3rem;                  
	font-weight: normal;             
	color: #000; 
	position: relative;   
	top: 10px;           
	margin: 0;                                   
	}
	.sales-header img {
	width: 350px;     
	height: auto;     
	display: block;    
	margin: 0 auto !important;   
	}

    .sort-dropdown {
        position: relative;
        margin-left: auto;
        margin-right: 200px;
        cursor: pointer;
    }

    /*Sort by */
    .sort-trigger {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .sort-label {
        color: gray;
        font-size: 1rem;
        font-weight: 600;
    }
    .sort-menu {
        font-weight: 600;
        position: absolute;
        top: 100%;
        left: 0;
        width: 120%; 
        background: #d6d6d6ff;
        border: 1px solid #9e9e9eff;
        border-radius: 6px;
        margin-top: 0.5rem;
        display: none;
        flex-direction: column;
        z-index: 1000;
    }
    .sort-menu a {
        padding: 0.6rem 0.8rem;
        text-decoration: none;
        color: #575757ff;
        font-size: 0.95rem;
    }
    .sort-menu a.active {
        background-color: #bfbfbf;
        color: #000;
        text-decoration: underline;
    }
    .sort-menu a:hover {
        background-color: #f2f2f2;
    }
    .sort-chevron {
        transition: transform 0.2s ease;
    }
    .sort-dropdown.open .sort-chevron {
        transform: rotate(90deg);
    }


</style>

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
		<main>
            <div class="hero">
				<div class="container">
					<div class="row justify-content-between">
						<div class="col-lg-5">
							<div class="intro-excerpt">
								<h1>Shop</h1>
								<p class="mb-4">Browse our full collection of premium barber supplies.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
            <div class="top-bar" id="top-bar">
                <?php 
                include __DIR__ . '/../includes/categ-toggle-button.php'; 
                ?>
                <form id="product-search-form" class="search-bar" action="" method="GET">
                    <?php 
                    foreach ($_GET as $key => $value) {
                        if ($key === 'query' || $key === 'page' || $key === 'sort') continue; 
                        echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
                    }
                    ?>
                    <input type="hidden" name="page" value="1">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query" placeholder="Search item..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <?php 
            include __DIR__ . '/../includes/categories-dropdown.php'; 
            ?>
>
            <div class="sales-header">
                <h2>
                    <?php
                        if (isset($_GET['favorites']) && (int)$_GET['favorites'] === 1) {
                            echo 'My Favorites';
                        } else if (isset($_GET['sale']) && (int)$_GET['sale'] === 1) {
                            echo 'Sales';
                        } else {
                            echo htmlspecialchars($categoryName);
                        }
                    ?>
                </h2>
                <img src="/barbershopSupplies/public/images/Ornament3.png" alt="Ornament">
            </div>

            <div class="sort-dropdown" id="sortDropdown">
                <div class="sort-trigger">
                    <span class="sort-label">Sort by</span>
                    <i class="fas fa-chevron-right sort-chevron"></i>
                </div>

                <?php $currentSort = $_GET['sort'] ?? 'default'; ?>
                <div class="sort-menu">
                    <a href="<?= buildLinkWithParams(['sort' => 'default', 'page' => 1]) ?>"
                        class="<?= $currentSort === 'default' ? 'active' : '' ?>">
                        Default
                    </a>
                    <a href="<?= buildLinkWithParams(['sort' => 'price_low', 'page' => 1]) ?>"
                        class="<?= $currentSort === 'price_low' ? 'active' : '' ?>">
                        Lowest price
                    </a>
                    <a href="<?= buildLinkWithParams(['sort' => 'price_high', 'page' => 1]) ?>"
                        class="<?= $currentSort === 'price_high' ? 'active' : '' ?>">
                        Highest price
                    </a>
                </div>
            </div>

            <?php 
            include __DIR__ . '/../includes/product-grid.php'; 
            include __DIR__ . '/../includes/paginator.php'; 
            ?>
  
        </main>
        
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            //Sort by dropdown
            const sortDropdown = document.getElementById('sortDropdown');
            const sortMenu = sortDropdown.querySelector('.sort-menu');
            const sortTrigger = sortDropdown.querySelector('.sort-trigger');
            sortDropdown.querySelector('.sort-trigger').addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = sortDropdown.classList.toggle('open');
                sortMenu.style.display = isOpen ? 'flex' : 'none';
            });
            document.addEventListener('click', () => {
                sortDropdown.classList.remove('open');
                sortMenu.style.display = 'none';
            });

		</script>
	</body>
</html>