<style>
    .product-grid {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr 1fr 1fr;
        gap: 10px;
        align-items: center;
        margin: 60px;
        text-align: center;
    }

    .header {
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid black;
        color: black;
        text-align: center;
    }

    @media (max-width: 768px) {

        .product-grid {
            display: block;
            margin: 15px 10px;
        }

        .product-grid .header {
            display: none;
        }

        .product-grid>div:not(.header) {
            border: 0px solid #000;
            padding: 10px;
            margin-bottom: 12px;
        }

        .order-number,
        .address,
        .total,
        .status,
        .action {
            display: block;
            width: 100%;
            text-align: left;
            padding: 5px 0;
            box-sizing: border-box;
        }

        .order-number::before {
            content: "Order: ";
            font-weight: bold;
        }

        .address::before {
            content: "Address: ";
            font-weight: bold;
        }

        .total::before {
            content: "Total: ";
            font-weight: bold;
        }

        .status::before {
            content: "Status: ";
            font-weight: bold;
        }

        .action {
            text-align: center;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 2px solid #5e5e5e !important;
        }
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