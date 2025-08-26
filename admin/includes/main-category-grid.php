<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';

    $perPage = 15;
    $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
    $currentPage = isset($_GET['main_page']) ? max(1, intval($_GET['main_page'])) : 1;
    $countSql = "SELECT COUNT(*) FROM main_categories WHERE 1=1";
    $countParams = [];
    if ($searchQuery !== '') {
        $countSql .= " AND name LIKE :search";
        $countParams[':search'] = '%' . $searchQuery . '%';
    }
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $perPage);

    $offset = ($currentPage - 1) * $perPage;
    $sql = "SELECT mc.id, mc.name, COUNT(sc.id) AS sub_count
        FROM main_categories mc
        LEFT JOIN categories sc ON sc.main_category_id = mc.id
        WHERE 1=1";
    $params = [];
    if ($searchQuery !== '') {
        $sql .= " AND mc.name LIKE :search"; 
        $params[':search'] = '%' . $searchQuery . '%';
    }
    $sql .= " GROUP BY mc.id, mc.name
            ORDER BY mc.name ASC
            LIMIT :limit OFFSET :offset";
    $params[':limit'] = $perPage;
    $params[':offset'] = $offset;
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .product-grid{
        display: grid;
        grid-template-columns: 1fr 1fr 200px;
        gap: 20px;
        align-items: center;
        margin: 40px 60px 40px 60px;
        text-align: center;
    }
    .header{
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid black;
        color: black;
        text-align: center;
    }
    .name.1 {
    }
    .name {
        outline: 0.5px solid black;
        background: #eeeeeeff;
        transition: outline 0.3s ease;
    }
    .name:focus {
        outline: 0.5px solid black;
        background: #dfdfdfff;
    }
    .category-row {
        display: contents; 
    }
</style>

<div class="product-grid">
    <div class="header name.1">Name</div>
    <div class="header number-sub">Sub categories</div>
    <div class="header action">Action</div>

    <?php
        if ($rows){
            foreach ($rows as $row){
                ?>
                <div class="category-row">
                    <div class="name" contenteditable="true" data-id="<?= $row['id']; ?>">
                        <?= htmlspecialchars($row['name']); ?>
                    </div>
                    <div class="number-sub"><?php echo $row['sub_count']; ?></div>
                    <div class="action">
                        <span class="delete-icon" data-id="<?= $row['id']; ?>" style="cursor: pointer; margin-left: 10px;">
                            <i class ="fas fa-trash" style="color: black;"></i>
                        </span>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No categories found.</p>";
        }
    ?>
</div>

<?php include 'paginator.php'; ?>