<style>
    .product-grid{
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
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
    .name {
        transition: outline 0.3s ease;
    }
    .name:focus {
        outline: 0.5px solid black;
        background: #e2e2e2;
    }
</style>

<div class="product-grid">
    <div class="header code">Code</div>
    <div class="header value">Value</div>
    <div class="header used-at">Used at</div>
    <div class="header order-number">Order number</div>

    <div class="code">USMGHABVL574</div>
    <div class="value">$100</div>
    <div class="used-at">Unused</div>
    <div class="order-number">Unused</div>
   
    <?php
    for ($i = 0; $i < 10; $i++) {
        ?>
        <div class="code">USMGHABVL574</div>
        <div class="value">$100</div>
        <div class="used-at">Unused</div>
        <div class="order-number">Unused</div>
    <?php }
        ?>
</div>