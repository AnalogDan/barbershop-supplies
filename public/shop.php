<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
	$currentPage = 'shop';

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
                <form id="product-search-form" class="search-bar" action="#" method="GET">
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
                    <h2>All</h2>
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