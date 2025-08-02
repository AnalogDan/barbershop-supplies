<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">

    <div class="container">
        <a class="navbar-brand" >New Vision Admin Panel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">
            <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                <li class="nav-item <?= ($currentPage === 'home') ? 'active' : '' ?>">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'products') ? 'active' : '' ?>">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'categories') ? 'active' : '' ?>">
                    <a class="nav-link" href="categories.php">Categories</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'orders') ? 'active' : '' ?>">
                    <a class="nav-link" href="orders.php">Orders</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'users') ? 'active' : '' ?>">
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'giftcards') ? 'active' : '' ?>">
                    <a class="nav-link" href="giftcards.php">Gift Cards</a>
                </li>
            </ul>
        </div>
    </div>
        
</nav>
<script src="/barbershopSupplies/public/js/bootstrap.bundle.min.js"></script>