<?php
    require_once __DIR__ . '/../../../config.php';
    require_once BASE_PATH . 'includes/db.php';
    $perPage = 15;
    $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
    $currentPage2 = isset($_GET['sub_page']) ? max(1, intval($_GET['sub_page'])) : 1;
    $countSql = "SELECT COUNT(*) 
                FROM categories c 
                JOIN main_categories mc ON c.main_category_id = mc.id
                WHERE 1=1";
    $countParams = [];
    if ($searchQuery !== '') {
        $countSql .= " AND c.name LIKE :search";
        $countParams[':search'] = '%' . $searchQuery . '%';
    }
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalItems = $countStmt->fetchColumn();
    $totalPages2 = ceil($totalItems / $perPage);

    $offset = ($currentPage2 - 1) * $perPage;
    $sql = "SELECT c.id, c.name, c.main_category_id, mc.name AS parent_name
        FROM categories c
        LEFT JOIN main_categories mc ON c.main_category_id = mc.id
        WHERE 1=1";
    $params = [];
    if ($searchQuery !== '') {
        $sql .= " AND c.name LIKE :search"; 
        $params[':search'] = '%' . $searchQuery . '%';
    }
    $sql .= " ORDER BY c.name ASC
            LIMIT :limit OFFSET :offset";
    $params[':limit'] = $perPage;
    $params[':offset'] = $offset;
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mainCategories = [];
    $stmt = $pdo->query("SELECT id, name FROM main_categories ORDER BY name ASC");
    while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mainCategories[] = $cat;
    }
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
    .parent-category{
        font-size: 13px;
        padding: 7px 12px;
        border: 0.5px solid #000;
        border-radius: 0px;
        background: #eeeeeeff;
    }
    .parent-category:focus{
        /* font-size: 13px;
        padding: 7px 12px;
        border: 0.5px solid #000;
        border-radius: 0px; */
        background: #dfdfdfff;
    }
    .subcategory-row {
        display: contents; 
    }
</style>

<div class="product-grid">
    <div class="header name.1">Name</div>
    <div class="header parent-categ">Parent category</div>
    <div class="header action">Action</div>

    <?php
    if ($rows) {
        foreach ($rows as $row) {
            ?>
            <div class="subcategory-row" data-id="<?= $row['id'] ?>">
                <div class="name" contenteditable="true" data-id="<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['name']) ?>
                </div>
                <select class="parent-category" data-id="<?= $row['id'] ?>">
                    <?php foreach ($mainCategories as $mainCat): ?>
                        <option value="<?= $mainCat['id'] ?>" 
                            <?= $mainCat['id'] == $row['main_category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mainCat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="action">
                    <span class="delete-icon" data-id="<?= $row['id'] ?>" style="cursor: pointer; margin-left: 10px;">
                        <i class="fas fa-trash" style="color: black;"></i>
                    </span>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No sub-categories found.</p>";
    }
    ?>
</div>

<?php
    include 'paginator2.php'; 
 ?>