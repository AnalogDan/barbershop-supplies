<?php
$perPage = 15;
$countSql = "SELECT COUNT(*) FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN main_categories m ON c.main_category_id = m.id
            WHERE 1=1";
$countParams = [];
if ($searchQuery !== ''){
    $countSql .= " AND p.name LIKE :search";
    $countParams[':search'] = '%' .$searchQuery . '%';
}
if ($mainCategoryId !== null) {
    $countSql .= " AND m.id = :main";
    $countParams[':main'] = $mainCategoryId;
}
if ($subCategoryId !== null) {
    $countSql .= " AND c.id = :subcategory";
    $countParams[':subcategory'] = $subCategoryId;
}
if ($outOfStock !== null) {
    $countSql .= " AND p.stock = 0";
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($countParams);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

$sql = "SELECT p.cutout_image, p.name, p.stock, p.id FROM products p 
        JOIN categories c ON p.category_id = c.id
        JOIN main_categories m ON c.main_category_id = m.id
        WHERE 1=1";
$params = [];

if ($searchQuery !== '') {
    $sql .= " AND p.name LIKE :search";
    $params[':search'] = '%' . $searchQuery . '%';
}

if ($mainCategoryId !== null) {
    $sql .= " AND m.id = :main";
    $params[':main'] = $mainCategoryId;
}

if ($subCategoryId !== null) {
    $sql .= " AND c.id = :subcategory";
    $params[':subcategory'] = $subCategoryId;
}

if ($outOfStock !== null) {
    $sql .= " AND p.stock = 0";
}

$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;
$sql .= " ORDER BY p.name ASC LIMIT :limit OFFSET :offset";
$params[':limit'] = $perPage;
$params[':offset'] = $offset;
$stmt = $pdo->prepare($sql);
foreach($params as $key => $value){
    $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key, $value, $paramType);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedCategoryLabel = '';
if (isset($_GET['subcategory'])) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$_GET['subcategory']]);
    $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($subcategory) {
        $selectedCategoryLabel = htmlspecialchars($subcategory['name']);
    }
} elseif (isset($_GET['main'])) {
    $stmt = $pdo->prepare("SELECT name FROM main_categories WHERE id = ?");
    $stmt->execute([$_GET['main']]);
    $mainCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($mainCategory) {
        $selectedCategoryLabel = htmlspecialchars($mainCategory['name']);
    }
} else {
    $selectedCategoryLabel = 'All';
}
?>

<style>
    .product-grid{
        display: grid;
        grid-template-columns: 200px 1fr 100px 200px;
        gap: 10px;
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
    .stock.1{
        outline: 0.5px solid black;
        background: #eeeeeeff;
        transition: outline 0.3s ease;
    }
    .stock {
        outline: 0.5px solid black;
        background: #eeeeeeff;
        transition: outline 0.3s ease;
    }
    .stock:focus {
        outline: 0.5px solid black;
        background: #dfdfdfff;
    }

    .product-row {
        display: contents;
    }
</style>

<div class="categ-admin-grid">
	<h3><?= $selectedCategoryLabel ?></h3>
</div>
<div class="product-grid">
    <div class="header thumbnail">Thumbnail</div>
    <div class="header name">Name</div>
    <div class="header stock.1">Stock</div>
    <div class="header actions">Actions</div>

    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-row" data-product-id="<?= $product['id'] ?>">
                <div class="thumbnail">
                    <img src="/barbershopSupplies/public/<?= htmlspecialchars($product['cutout_image']) ?>" 
                        alt="Product Thumbnail" style="width: 60px; height: 60px; object-fit: contain;">
                </div>
                <div class="name"><?= htmlspecialchars($product['name']) ?></div>
                <div class="stock" contenteditable="true" data-product-id="<?= $product['id'] ?>">
                    <?= intval($product['stock']) ?>
                </div>
                <div class="actions">
                    <a href="products-edit.php?id=<?= $product['id'] ?>" class="edit-link" style="text-decoration: underline; cursor: pointer;">Edit details</a>
                    <span class="delete-icon" style="cursor: pointer; margin-left: 10px;">
                        <i class="fas fa-trash" style="color: black;"></i>
                    </span>
                </div>
            </div>  
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-orders" style="grid-column: 1 / -1; text-align: center; padding: 1rem;">
            No products found.
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/modals.php'; ?>

<script>
    document.querySelectorAll('.stock[contenteditable="true"]').forEach(div => {
        div.addEventListener('blur', function () {
            const productId = this.dataset.productId;
            const newStock = this.textContent.trim();

            fetch('/barbershopSupplies/admin/includes/update-stock.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    new_stock: newStock
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('Stock updated!');
                } else {
                    console.error('Error updating stock:', data.message);
                    alert('Failed to update stock.');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Network error.');
            });
        });
    });

    
    document.querySelectorAll('.delete-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            showConfirmModal(
                "Delete product?",
                () => {
                        const productRow = icon.closest('.product-row');
                        const productId = productRow.dataset.productId;
                        fetch('/barbershopSupplies/admin/includes/products-delete-handler.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({ id: productId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success){
                                productRow.remove();
                            } else {
                                alert('Failed to delete product: ' + data.message);
                            }
                        })
                        .catch(() => alert('Something went wrong.'));
                    },
                () => {}     
            );
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