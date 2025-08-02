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
    .product-form{
        max-width: 500px;
        width: 100%;
        margin: 140px auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .product-form label{
        font-size: 20px;
        font-weight: bold;
        color: black;
        margin-bottom: 5px;
        display: block;
    }
    .product-form input,
    .product-form select{
        width: 100%;
        height: 45px;
        padding: 10px;
        border: 0.5px solid #000;
        background-color: #e2e2e2;
    }
    .product-form input:focus{
        outline: none;
        box-shadow: 0 0 0 1px #7f7f7f;
    }
    .button-row {
        display: flex;
        gap: 60px; 
        justify-content: center;
        width: fit-content;
        margin: 0 auto;
    }

    .side-by-side{
        display: flex;
        gap: 10px;
    }
    .side-by-side input:first-child{
        flex:7;
    }
    .side-by-side input:last-child{
        flex:3;
    }
</style>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'giftcards'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <form class="product-form">
                <label for="letters">Code</label>
                <div class="side-by-side">
                    <input type="text" id="letters" name="letters" placeholder="ABCDEF">
                    <input type="text" id="numbers" name="numbers" placeholder="1234">
                </div>
                <label for="value">Value</label>
                <select id="value" name="value">
                    <option value="">Select a value</option>
                    <option value="50">$50</option>
                    <option value="100">$100</option>
                    <option value="150">$150</option>
                    <option value="200">$200</option>
                    <option value="300">$300</option>
                </select>
                <div class="button-row">
                    <button type="submit" class="btn btn-5">Add</button>
                    <a href="giftcards.php" class="btn btn-6">Cancel</a>
                </div>
            </form>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
    </body>
</html>