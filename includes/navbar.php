<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">

    <div class="container">
        <a class="navbar-brand" >New Vision Barber Supplies</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">
            <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                <li class="nav-item <?= ($currentPage === 'home') ? 'active' : '' ?>">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'shop') ? 'active' : '' ?>">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'contactUs') ? 'active' : '' ?>">
                    <a class="nav-link" href="contactUs.php">Contact Us</a>
                </li>
                <li class="nav-item <?= ($currentPage === 'cart') ? 'active' : '' ?>">
                    <a class="nav-link" href="cart.php">
                        <img src="../public/images/cart.png" alt="Cart" style="height:24px; width:auto;">
                    </a>
                </li>
                <li class="nav-item <?= ($currentPage === 'account') ? 'active' : '' ?>">
                    <a class="nav-link" href="login.php">
                        <img src="../public/images/account.png" alt="account" style="height:24px; width:auto;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
        
</nav>
<script src="/barbershopSupplies/public/js/bootstrap.bundle.min.js"></script>