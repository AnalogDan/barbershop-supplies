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
    .name {
        transition: outline 0.3s ease;
    }
    .name:focus {
        outline: 0.5px solid black;
        background: #e2e2e2;
    }
</style>

<div class="product-grid">
    <div class="header name">Name</div>
    <div class="header email">Email</div>
    <div class="header created-at">Created at</div>

    <div class="name">Bruce Wayne</div>
    <div class="email">batboy5@gmail.com</div>
    <div class="created-at">02-02-2025</div>
    

    <?php
    for ($i = 0; $i < 10; $i++) {
        ?>
        <div class="name">Bruce Wayne</div>
        <div class="email">batboy5@gmail.com</div>
        <div class="created-at">02-02-2025</div>
    <?php }
        ?>
</div>