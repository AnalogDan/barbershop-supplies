<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = '';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $userId = $_SESSION['user_id'] ?? null;

    //Pagination operations
    $ordersPerPage = 10;
    $currentPageNum = isset($_GET['page']) && is_numeric($_GET['page'])
        ? (int) $_GET['page']
        : 1;
    if ($currentPageNum < 1) {
        $currentPageNum = 1;
    }
    $offset = ($currentPageNum - 1) * $ordersPerPage;

    $countStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM orders
        WHERE user_id = ?
    ");
    $countStmt->execute([$userId]);
    $totalOrders = (int) $countStmt->fetchColumn();
    $totalPages = (int) ceil($totalOrders / $ordersPerPage);

    //Fetch orders
    if (!$userId) {
        header('Location: login.php');
        exit;
    }
    $stmt = $pdo->prepare("
        SELECT id, number, status, total, placed_at
        FROM orders
        WHERE user_id = :userId
        ORDER BY placed_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $ordersPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    /*Top row*/
    .go-back{
        color: #6f6f6fff;
        height: 3.5rem;
        width: 10%;
        display: flex;
        align-items: center;
        justify-content: flex-end; 
        text-decoration: none;
    }
    .back-icon{
        margin-top: 9rem;
        font-size: 3.5rem;
        width: 3.5rem;
        transition: color 0.2s ease, transform 0.2s ease;
        cursor: pointer;
	}
	.back-icon:hover {
        color: black;
	}
    
    .top-container{
        height: 10rem;
        width: 80%;
        margin: 0 auto 0 auto;
        display: flex;
        flex-direction: column;     
        justify-content: center;    
        align-items: center;   
        color: black;
        font-family: 'OldLondon', serif;
        font-size: 3rem;
    }
    .top-container img{
        height: 5rem;
    }

    /*Orders*/
    .orders-list{
        height: auto;
        width: auto;
        /* background-color: lightblue; */
        margin: 2rem auto 10rem auto;
        color: #6f6f6fff;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .view{
        color: #6f6f6fff;
        text-decoration: underline;
        cursor: pointer;
    }
    .order-row{
        justify-content: center;   
        display: flex;
        margin-bottom: 1.2rem;
    }

</style>
<link rel="stylesheet" href="css/my-orders.mobile.css">


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
								<h1>My profile</h1>
								<p class="mb-4">Manage your account details, orders and favorites.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

            <a href="my-profile.php" class="go-back">
                <i class="fa-solid fa-circle-chevron-left back-icon"></i>
            </a>
            <div class="top-container">
                <div>All orders</div>
                <img src="images/Ornament1.png">
            </div>

            <div class="orders-list">
                <?php if (empty($orders)): ?>
                    <div class="order-row">
                        <div>You have no orders yet.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-row">
                            <div>
                                Order #<?= htmlspecialchars($order['number']) ?>
                                - <?= date('M d, Y', strtotime($order['placed_at'])) ?>
                                - $<?= number_format($order['total'], 2) ?>
                                - <?= ucfirst($order['status']) ?> -
                            </div>
                            <a href="order.php?id=<?= $order['id'] ?>" class="view">
                                View
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            </div>

            <?php
            if ($totalPages > 1) {
                $totalPages = $totalPages ?? 1;
                include __DIR__ . '/../includes/paginator-orders.php';
            }
            ?>
            
        </main>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            
		</script>
    </body>
</html>