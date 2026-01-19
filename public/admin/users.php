<?php
    session_start();

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
	require_once __DIR__ . '/../../config.php';
    require_once BASE_PATH . 'includes/db.php';

    $currentPagee = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
    $limit = 10;
    $offset = ($currentPagee - 1) * $limit;
    $sqlCount = "SELECT COUNT(*) FROM users";
    $params = [];

    if (!empty($searchQuery)) {
        $sqlCount .= " WHERE first_name LIKE :query OR last_name LIKE :query OR email LIKE :query";
        $params[':query'] = '%' . $searchQuery . '%';
    }

    $stmt = $pdo->prepare($sqlCount);
    $stmt->execute($params);
    $totalUsers = $stmt->fetchColumn();
    $totalPages = ceil($totalUsers / $limit);

    $sqlUsers = "SELECT id, first_name, last_name, email, created_at FROM users";
    if (!empty($searchQuery)) {
        $sqlUsers .= " WHERE first_name LIKE :query OR last_name LIKE :query OR email LIKE :query";
    }
    $sqlUsers .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sqlUsers);
    if (!empty($searchQuery)) {
        $stmt->bindValue(':query', '%' . $searchQuery . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'users'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="top-bar">
                <div class="tiny-message">The users info is view-only.</div>
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query" placeholder="Search name or email..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </main>
        <?php include 'includes/users-grid.php'; ?>
        <?php 
        include 'includes/paginator4.php'; 
        ?>
        <?php include 'includes/admin_footer.php'; ?>
    </body>
</html>