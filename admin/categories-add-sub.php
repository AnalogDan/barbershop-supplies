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
    .product-form select,
    .product-form textarea{
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

</style>

<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'categories'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <form class="product-form">
                <label for="name">Sub category name</label>
                <input type="text" id="name" name="name">
                <label for="category">Main category</label>
                <select id="category" name="category">
                    <option value="">Select a category</option>
                    <option value="clippers">Tools & electricals</option>
                    <option value="combs">Hair products</option>
                    <option value="scissors">Beard care</option>
                </select>
                <div class="button-row">
                    <button type="submit" class="btn btn-5">Add</button>
                    <a href="categories.php" class="btn btn-6">Cancel</a>
                </div>
            </form>
        </main>
        <?php include 'includes/admin_footer.php'; ?> 
    </body>
</html>