<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
	require_once __DIR__ . '/../includes/db.php';

    $currentPagee = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
    $allowedFilters = ['all', 'unused', 'used'];
    $filter = isset($_GET['filter']) && in_array($_GET['filter'], $allowedFilters) ? $_GET['filter'] : 'all';
    $limit = 10;
    $offset = ($currentPagee - 1) * $limit;
    $whereClauses = [];
    $params = [];

    if (!empty($searchQuery)) {
        $whereClauses[] = "code LIKE ?";
        $params[] = "%" . $searchQuery . "%";
    }
    if ($filter === 'unused') {
        $whereClauses[] = "used_at IS NULL";
    } elseif ($filter === 'used') {
        $whereClauses[] = "used_at IS NOT NULL";
    }
    $whereSQL = '';
    if (!empty($whereClauses)) {
        $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
    }

    $sqlCount = "SELECT COUNT(*) FROM gift_cards $whereSQL";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    $sql = "SELECT code, value, used_at, order_id, id
            FROM gift_cards
            $whereSQL
            ORDER BY id DESC
            LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $giftCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'giftcards'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="top-bar">
                <div class="category-toggle order-version">
                    <div class="toggle-option" data-target="all">All</div>
                    <div class="toggle-option" data-target="unused">Unused</div>
                    <div class="toggle-option" data-target="used">Used</div>
                </div>
                <form id="product-search-form" class="search-bar" action="#" method="GET">
                    <input type="hidden" name="filter" id="filter-input" value="<?= htmlspecialchars($filter) ?>">
                    <div class="search-wrapper">
                        <input type="text" name="query" id="search-query" placeholder="Search code..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"/>
                        <button type="submit" class="search-button" aria-label="Search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <a href="giftcards-add.php" id="add-button" class="btn btn-third">Add</a>
            <?php include 'includes/giftcards-grid.php'; ?>
        </main>
        <?php 
        include 'includes/paginator5.php'; 
        ?>
        <?php include 'includes/admin_footer.php'; ?>
        <?php include 'includes/modals.php'; ?>
    

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
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.delete-icon');
                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        showConfirmModal(
                            "Delete gift card?",
                            () => {
                                const giftCardId = this.dataset.id;
                                console.log(giftCardId);
                                fetch('includes/delete-giftcard.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'id=' + encodeURIComponent(giftCardId)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Remove the gift card row from the grid
                                        const parentRow = this.closest('.giftcard-row'); 
                                        if (parentRow) parentRow.remove();
                                    } else {
                                        console.error(data.message || 'Delete failed');
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                                },
                            () => {}     
                        );
                    });
                });
            });
            function showConfirmModal(message, onYes, onNo) {
                const template = document.getElementById('confirmModal');
                const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
                document.body.appendChild(modal);
                modal.querySelector('p').textContent = message;
                modal.classList.add('show');
                const yesBtn = modal.querySelector('#confirmYes');
                const noBtn = modal.querySelector('#confirmNo');
                function cleanup() {
                    yesBtn.removeEventListener('click', yesHandler);
                    noBtn.removeEventListener('click', noHandler);
                    modal.remove();
                }
                function yesHandler() {
                    cleanup();
                    if (typeof onYes === 'function') onYes();
                }
                function noHandler() {
                    cleanup();
                    if (typeof onNo === 'function') onNo();
                }
                yesBtn.addEventListener('click', yesHandler);
                noBtn.addEventListener('click', noHandler);
            }
        </script>
    </body>
</html>