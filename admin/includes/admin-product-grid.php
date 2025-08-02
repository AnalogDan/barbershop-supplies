<?php
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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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
    .stock {
        transition: outline 0.3s ease;
    }
    .stock:focus {
        outline: 0.5px solid black;
        background: #e2e2e2;
    }
</style>

<div class="categ-admin-grid">
	<h3><?= $selectedCategoryLabel ?></h3>
</div>
<div class="product-grid">
    <div class="header thumbnail">Thumbnail</div>
    <div class="header name">Name</div>
    <div class="header stock">Stock</div>
    <div class="header actions">Actions</div>

    <?php foreach ($products as $product): ?>
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
    <?php endforeach; ?>
</div>

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
</script>