<?php
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'account';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    //Fetch user and address info
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT first_name, last_name, email, phone
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare("
        SELECT full_name, street, city, state, zip
        FROM user_addresses
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    //Fetch favorites
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.name,
            p.cutout_image
        FROM favorites f
        JOIN products p ON p.id = f.product_id
        WHERE f.user_id = ?
        ORDER BY f.product_id DESC
        LIMIT 3
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Fetch orders
    $stmt = $pdo->prepare("
        SELECT id, number, status
        FROM orders
        WHERE user_id = ?
        ORDER BY id DESC
        LIMIT 3
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /*Main structure*/
    .giant-container{
        padding: 50px 10% 2rem 10%;
    }
	.section {
        border: 1px solid #5b5b5bff;
        border-bottom: 0;
    }
    .section-header {
        position: relative;
        color: #3b3b3bff;
        background: #dededeff;
        padding: 15px;
        font-weight: 600;
        font-size: 1.3rem;
        cursor: pointer;
    }
    .section-content {
        border-top: 1px solid black;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.9s ease;
        background: #ccccccff;
    }

    
    .section:nth-child(odd) .section-header {
        background: #dedede;   
    }
    .section:nth-child(even) .section-header {
        background: #cccccc;  
    }
    .section:nth-child(odd) .section-content {
        background: #dedede;
    }
    .section:nth-child(even) .section-content {
        background: #cccccc;
    }
    .section:last-child .section-content {
        border-bottom: 1px solid #5b5b5bff;;
    }


    .chevron {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%) rotate(0deg);
        font-size: 1.9rem;
        color: #333;
        transition: transform 0.25s ease;
        pointer-events: none;
    }
    .section.open .chevron {
        transform: translateY(-50%) rotate(90deg);
    }

    /*User info*/
    .user-info{
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1rem
    }
    .user-change{
        display: block;
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1.3rem;
        margin-bottom: 1.3rem;
        text-decoration: underline;
    }


    /*My favorites*/
    .favs{
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
    }
     .item-row{
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin: 0.7rem auto;
        height: 5rem;
        gap: 1rem; 
        display: flex;
        align-items: center;  
    }
    .item-row img{
        height: 6.5rem;
        width: auto;
    }
    .item-name{
        width: 15rem;
    }

	.item-row i {
	cursor: pointer;
	font-size: 20px;
	transition: color 0.2s ease, transform 0.2s ease;
	}
	.item-row i:hover {
	color: black;
	transform: scale(1.15);
	}
    .btn{
        border-radius: 13px !important;
        display: block;
        margin: 0 auto 1rem auto;
        width: auto;
    }
    .spacer{
        height: 0.1rem;
        width: 100px;
        flex-shrink: 0;
    }
    .fav-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: inherit;
        text-decoration: none;
    }

    .fav-link:hover .item-name {
        text-decoration: underline;
    }
    
    /*Order history*/
    .order{
        width: auto;
        display: inline-block;
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1.3rem;
        /* margin-bottom: 1.3rem; */
        text-decoration: underline;
    }
    .order-end{
        width: auto;
        display: inline-block;
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1.3rem;
        margin-bottom: 1.3rem;
        text-decoration: underline;
    }

    .log-out{
        display: inline-block;
        margin: 0 auto 5rem auto;
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
								<h1>My profile</h1>
								<p class="mb-4">Manage your account details, orders and favorites.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

            <div class="giant-container">
                <div class="section">
                    <div class="section-header">
                        User information
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                       <div class="user-info">
                            Name: <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                        </div>
                        <div class="user-info">
                            Email: <?= htmlspecialchars($user['email']) ?>
                        </div>
                        <div class="user-info">
                            Address: 
                            <?php if ($address): ?>
                                <?= htmlspecialchars(
                                    $address['street'] . ', ' .
                                    $address['city'] . ', ' .
                                    $address['state'] . ' ' .
                                    $address['zip']
                                ) ?>
                            <?php else: ?>
                                <em>No address added</em>
                            <?php endif; ?>
                        </div>
                        <!-- <a href="#" class="user-change">Change name</a>
                        <a href="#" class="user-change">Change email</a> -->
                        <a href="change-address.php" class="user-change">Change address</a> 
                        <a href="change-password.php" class="user-change">Change password</a> 
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        My favorites
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content debug-fav">
                        <div class="favs">
                            <?php if (!empty($favorites)): ?>
                                <?php foreach ($favorites as $fav): ?>
                                    <div class="item-row">
                                        <a 
                                            href="product.php?id=<?= $fav['id'] ?>" 
                                            class="fav-link"
                                        >
                                            <span>
                                                <img src="<?= htmlspecialchars($fav['cutout_image']) ?>" alt="">
                                            </span>

                                            <span class="item-name">
                                                <?= htmlspecialchars($fav['name']) ?>
                                            </span>
                                        </a>
                                        <span class="trash" data-product-id="<?= $fav['id'] ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </span>
                                    </div>
                                <?php endforeach; ?>

                                <a href="shop.php?page=1&favorites=1" class="btn check-btn">See all</a>

                            <?php else: ?>
                                <p style="
                                    text-align: center;
                                    height: 300px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                ">
                                    You have no favorites yet.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        Order history
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <a 
                                    href="order.php?id=<?= $order['id'] ?>" 
                                    class="order"
                                >
                                    Order #<?= htmlspecialchars($order['number']) ?> – <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                </a><br>
                            <?php endforeach; ?>

                            <a href="my-orders.php" class="order-end">See all</a>
                        <?php else: ?>
                            <p style="
                                height: 100px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                You have no orders yet.
                            </p>
                        <?php endif; ?> 
                    </div>
                </div>
            </div>

            <div class="log-out">
                <a href="../includes/logout.php" class="btn btn-secondary me-2 my-btn-custom">Log out</a>
            </div>
        </main>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            /*Expand dropdowns*/
			document.querySelectorAll('.section-header').forEach(header => {
                header.addEventListener('click', () => {
                    const section = header.parentElement;
                    const content = section.querySelector('.section-content');
                    const chevron = header.querySelector('.chevron');

                    const isOpen = section.classList.toggle('open');

                    if (isOpen) {
                        content.style.maxHeight = (content.scrollHeight + 24) + "px";
                    } else {
                        content.style.maxHeight = "0px";
                    }
                });
            });

            //Remove favorite 
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.trash').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        e.preventDefault();
                        const productId = btn.dataset.productId;
                        removeFavorite(productId, btn);
                    });
                });
            });

        function removeFavorite(productId, btn) {
            fetch('<?= BASE_URL ?>actions/remove-favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = btn.closest('.item-row');
                    row.remove();
                    if (!document.querySelector('.item-row')) {
                        document.querySelector('.favs').innerHTML = `
                            <p style="padding: 15px; opacity: 0.7;">
                                You have no favorites yet.
                            </p>
                        `;
                    }
                } else {
                    showAlertModal(data.message || 'Could not remove favorite.');
                }
            })
            .catch(() => {
                showAlertModal('Server error. Please try again.');
            });
        }
		</script>
    </body>
</html>