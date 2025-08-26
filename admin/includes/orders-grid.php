<style>
    .product-grid{
        display: grid;
        grid-template-columns: 1fr 2fr 1fr 1fr 1fr;
        gap: 10px;
        align-items: center;
        margin: 60px;
        text-align: center;
    }
    .header{
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid black;
        color: black;
        text-align: center;
    }
</style>

<div class="product-grid">
    <div class="header order-number">Order number</div>
    <div class="header address">Address</div>
    <div class="header total">Total</div>
    <div class="header status">Status</div>
    <div class="header action">Action</div>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-number"><?= htmlspecialchars($order['number']) ?></div>
            <div class="address"><?= htmlspecialchars($order['city'] . ', ' . $order['state']) ?></div>
            <div class="total">$<?= number_format($order['total'], 2) ?></div>
            <div class="status"><?= htmlspecialchars($order['status']) ?></div>
            <div class="action">
                <a href="orders-details.php?order=<?= urlencode($order['number']) ?>" 
                   class="see-link" style="text-decoration: underline; cursor: pointer;">
                   See details
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-orders" style="grid-column: 1 / -1; text-align: center; padding: 1rem;">
            No orders found.
        </div>
    <?php endif; ?>
</div>