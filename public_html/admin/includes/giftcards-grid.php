<style>
    .product-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        gap: 20px;
        align-items: center;
        margin: 40px 60px 40px 60px;
        text-align: center;
    }

    .header {
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid black;
        color: black;
        text-align: center;
    }

    .name {
        transition: outline 0.3s ease;
    }

    .name:focus {
        outline: 0.5px solid black;
        background: #e2e2e2;
    }

    .giftcard-row {
        display: contents;
        /* keeps the children aligned with the main grid */
    }

    @media (max-width: 768px) {

        .product-grid {
            display: block;
            margin: 15px 10px;
        }

        /* Hide desktop headers */
        .product-grid .header {
            display: none;
        }

        /* Each gift card becomes a card */
        .giftcard-row {
            display: block;
            border: 1px solid black;
            padding: 12px;
            margin-bottom: 12px;
            background: #fff;
        }

        .code,
        .value,
        .used-at,
        .order-number,
        .action {
            display: block;
            width: 100%;
            text-align: left;
            margin-bottom: 8px;
            box-sizing: border-box;
        }

        .code::before {
            content: "Code: ";
            font-weight: bold;
        }

        .value::before {
            content: "Value: ";
            font-weight: bold;
        }

        .used-at::before {
            content: "Used at: ";
            font-weight: bold;
        }

        .order-number::before {
            content: "Order: ";
            font-weight: bold;
        }

        .action {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0;
            padding-top: 8px;
        }
    }
</style>

<div class="product-grid">
    <div class="header code">Code</div>
    <div class="header value">Value</div>
    <div class="header used-at">Used at</div>
    <div class="header order-number">Order number</div>
    <div class="header order-number">Action</div>

    <?php if (!empty($giftCards)): ?>
        <?php foreach ($giftCards as $card): ?>
            <div class="giftcard-row">
                <div class="code"><?= htmlspecialchars($card['code']) ?></div>
                <div class="value">$<?= number_format($card['value'], 2) ?></div>
                <div class="used-at">
                    <?= $card['used_at'] ? htmlspecialchars($card['used_at']) : 'Unused' ?>
                </div>
                <div class="order-number">
                    <?= $card['order_id'] ? htmlspecialchars($card['order_id']) : 'Unused' ?>
                </div>
                <div class="action">
                    <span class="delete-icon" data-id="<?= $card['id']; ?>" style="cursor: pointer; margin-left: 10px;">
                        <i class="fas fa-trash" style="color: black;"></i>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-orders" style="grid-column: 1 / -1; text-align: center; padding: 1rem;">
            No gift cards found.
        </div>
    <?php endif; ?>
</div>