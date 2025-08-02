<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<?php
	require_once __DIR__ . '/../includes/db.php';
?>
<!DOCTYPE html>

<style>
    .tiny-message{
        margin: 60px 60px;
        font-size: 17px;
        font-weight: bold;
        color: #929292ff;
    }

    .info-column{
        display: flex;
        flex-direction: column;
        gap: 30px;
        font-size: 17px;
        color: black;
        margin-bottom: 10px;
    }
    .info-row{
        margin-left: 60px;
        display: flex;
        gap: 8px;
    }
    .label{
        font-weight: bold;
        min-width: 160px;
    }
    .value{
        color: #929292ff;
        font-weight: bold;
    }
    .status-drop{
        font-size: 16px;
        padding: 7px 12px;
        border: 0.5px solid #000;
        border-radius: 0px;
        background-color: #e2e2e2;
    }

    .product-grid{
        margin: 0 auto;
        width: 60%;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }
    .product-row{
        display: grid;
        grid-template-columns: auto 40px 1fr auto;
        align-items: center;
        gap: 10px;
        padding: 6px 15px;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }
    .product-row > * {
        justify-self: center;
    }
    .thumbnail img{
        width: 60px;
        height: 60px;
        object-fit: cover;
    }
    .button-row {
        display: flex;
        gap: 60px; 
        justify-content: center;
        width: fit-content;
        margin: 60px auto;
    }
</style>

<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'orders'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
           <div class="tiny-message">You can only edit the order status.</div>
            <div class="info-column">
                <div class="info-row">
                    <span class="label">Order number: </span>
                    <span class="value">#12345</span>
                </div>
                <div class="info-row">
                    <span class="label">Shipping name: </span>
                    <span class="value">Bruce Wayne</span>
                </div>
                <div class="info-row">
                    <span class="label">Shipping address: </span>
                    <span class="value">2546 Sociosqu Rd. Bethlehem Utah</span>
                </div>
                <div class="info-row">
                    <span class="label">Phone: </span>
                    <span class="value">392 123 1234</span>
                </div>
                <div class="info-row">
                    <span class="label">Placed on date: </span>
                    <span class="value">7/24/2025</span>
                </div>
                <div class="info-row">
                    <span class="label">Est. delivery date: </span>
                    <span class="value">8/24/2025 - 8/28/2025</span>
                </div>
                <div class="info-row">
                    <span class="label">Payment method: </span>
                    <span class="value">Visa ****4819</span>
                </div>
                <div class="info-row">
                    <span class="label">Subtotal: </span>
                    <span class="value">$400.00</span>
                </div>
                <div class="info-row">
                    <span class="label">Sales tax: </span>
                    <span class="value">$10.00</span>
                </div>
                <div class="info-row">
                    <span class="label">Shipping cost: </span>
                    <span class="value">$20.00</span>
                </div>
                <div class="info-row">
                    <span class="label">Total price: </span>
                    <span class="value">$430</span>
                </div>
                <div class="info-row">
                    <span class="label">Order status: </span>
                    <select id="status" name="status" class="status-drop">
                        <option value="delivered">Delivered</option>
                        <option value="in-transit">In-transit</option>
                        <option value="processing">Processing</option>
                        <option value="canceled">Canceled</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="info-row">
                    <span class="label">List of products: </span>
                </div>
                <div class="product-grid">
                    <div class="product-row">
                        <div class="qty">2x</div>
                        <div class="thumbnail">
                            <img src="/barbershopSupplies/public/images/products/test.webp" alt="Trimmer">
                        </div>
                        <div class="name">Wahl Cordless Senior Clipper</div>
                        <div class="price">$300</div>
                    </div>
                    <div class="product-row">
                        <div class="qty">1x</div>
                        <div class="thumbnail">
                            <img src="/barbershopSupplies/public/images/products/test3.webp" alt="Trimmer">
                        </div>
                        <div class="name">JRL Onyx 2020C Clipper</div>
                        <div class="price">$100</div>
                    </div>
                </div>
            </div>
            <div class="button-row">
                <button class="btn btn-5">Save changes</button>
                <a href="orders.php" class="btn btn-6">Cancel</a>
            </div>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
    </body>
</html>