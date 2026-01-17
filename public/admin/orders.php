<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
    }
	require_once __DIR__ . '/../includes/db.php';

    $currentPagee = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
    $allowedFilters = ['week', 'month', 'year', 'all'];
    $filter = isset($_GET['filter']) && in_array($_GET['filter'], $allowedFilters) ? $_GET['filter'] : 'all';
    $limit = 10;
    $offset = ($currentPagee - 1) * $limit;
    $whereClauses = [];
    $params = [];

    if (!empty($searchQuery)) {
        $whereClauses[] = "o.number LIKE ?";
        $params[] = "%" . $searchQuery . "%";
    }
    switch ($filter) {
        case 'week':
            $whereClauses[] = "o.placed_at >= NOW() - INTERVAL 7 DAY";
            break;
        case 'month':
            $whereClauses[] = "o.placed_at >= NOW() - INTERVAL 1 MONTH";
            break;
        case 'year':
            $whereClauses[] = "o.placed_at >= NOW() - INTERVAL 1 YEAR";
            break;
    }
    $whereSQL = "";
    if (!empty($whereClauses)) {
        $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
    }

    $countSQL = "SELECT COUNT(*) as total
             FROM orders o
             JOIN addresses a ON o.address_id = a.id
             $whereSQL";
    $stmt = $pdo->prepare($countSQL);
    if (!empty($params)) {
        foreach ($params as $i => $value) {
            $stmt->bindValue($i + 1, $value);
        }
    }
    $stmt->execute();
    $totalRows = $stmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    $sql = "SELECT o.number, a.city, a.state, o.total, o.status, o.placed_at
            FROM orders o
            JOIN addresses a ON o.address_id = a.id
            $whereSQL
            ORDER BY o.placed_at DESC
            LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params); 
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'orders'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="top-bar">
                <div class="category-toggle order-version">
                    <div class="toggle-option" data-target="week">Last week</div>
                    <div class="toggle-option" data-target="month">Last month</div>
                    <div class="toggle-option" data-target="year">Last year</div>
                    <div class="toggle-option" data-target="all">All</div>  
                </div>
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <input type="hidden" name="filter" id="filter-input" value="<?= htmlspecialchars($filter) ?>">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query" placeholder="Search order number..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <?php include 'includes/orders-grid.php'; ?>
            <?php 
            include 'includes/paginator3.php'; 
            ?>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <script>
            const options = document.querySelectorAll('.category-toggle .toggle-option');
            options.forEach(option => {
                option.addEventListener('click', () => {
                    options.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    const selected = option.dataset.target;
                    console.log("Selected:", selected);
                    const url = new URL(window.location.href);
                    url.searchParams.set('filter', selected);
                    url.searchParams.set('page', 1); 
                    window.location.href = url.toString();
                });
            });
            const currentFilter = "<?= $filter ?>"; 
            options.forEach(opt => {
                if (opt.dataset.target === currentFilter) {
                    opt.classList.add('active');
                }
            });
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('product-search-form');
                const filterInput = document.getElementById('filter-input');

                form.addEventListener('submit', function(e) {
                    e.preventDefault(); 
                    const query = document.getElementById('search-query').value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('query', query);
                    url.searchParams.set('filter', filterInput.value);
                    url.searchParams.set('page', 1);

                    console.log('Redirecting to:', url.toString());
                    window.location.href = url.toString();
                });
            });
        </script>
    </body>
</html>