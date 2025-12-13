<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'shop';

    //Variables for pagination
    $productsPerPage = 4;
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
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image
                FROM products
                WHERE category_id = ?
                AND name LIKE ?
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $subCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(3, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image
                FROM products
                WHERE category_id = ?
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $subCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
    } else if ($mainCategoryId) {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.sale_start, p.sale_end, p.cutout_image
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.main_category_id = ?
                AND p.name LIKE ?
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $mainCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(3, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.sale_start, p.sale_end, p.cutout_image
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.main_category_id = ?
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $mainCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
    } else {
        if ($searchQuery) {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image
                FROM products
                WHERE name LIKE ?
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, "%{$searchQuery}%", PDO::PARAM_STR);
            $stmt->bindValue(2, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("
                SELECT id, name, price, sale_price, sale_start, sale_end, cutout_image
                FROM products
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $productsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $totalProducts = (int) $stmt->fetchColumn();
    $totalPages = (int) ceil($totalProducts / $productsPerPage);


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
    margin-left: auto;        
    margin-right: 200px;
    }
    .sort-label {
    color: gray;
    font-size: 1rem;
    font-weight: 600;
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
            <div class="top-bar">
                <?php 
                include __DIR__ . '/../includes/categ-toggle-button.php'; 
                ?>
                <form id="product-search-form" class="search-bar" action="" method="GET">
                    <?php 
                    foreach ($_GET as $key => $value) {
                        if ($key === 'query' || $key === 'page') continue; 
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
                    <h2><?php echo htmlspecialchars($categoryName); ?></h2>
                    <img src="/barbershopSupplies/public/images/Ornament3.png" alt="Ornament">
            </div>
            <div class="sort-dropdown">
                <span class="sort-label">Sort by</span>
                <i class="fas fa-chevron-down"></i>
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

		</script>
	</body>
</html>