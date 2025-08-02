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

    <div class="order-number">#18006</div>
    <div class="address">Colorado, CA</div>
    <div class="total">$559.99</div>
    <div class="status">Delivered</div>
    <div class="action">
        <a href="orders-details.php" class="see-link" style="text-decoration: underline; cursor: pointer;">See details</a>
    </div>

    <?php
    for ($i = 0; $i < 10; $i++) {
        ?>
        <div class="order-number">#18006</div>
        <div class="address">Colorado, CA</div>
        <div class="total">$559.99</div>
        <div class="status">Delivered</div>
        <div class="action">
            <a href="orders-details.php" class="see-link" style="text-decoration: underline; cursor: pointer;">See details</a>
        </div>
    <?php }
        ?>
</div>