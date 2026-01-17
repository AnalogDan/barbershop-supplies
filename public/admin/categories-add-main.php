<?php
require_once __DIR__ . '/../../config.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<?php
	require_once __DIR__ . '/../../config.php';
    require_once BASE_PATH . 'includes/db.php';
?>
<!DOCTYPE html>

<style>
    .product-form{
        max-width: 500px;
        width: 100%;
        margin: 50px auto 140px auto;
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
            <form class="product-form" id="add-main-form" method="POST">
                <div class="section-title">
                    <h2>Add main category</h2>
                </div>
                <label for="name">Main category name</label>
                <input type="text" id="name" name="name">
                <div class="button-row">
                    <button type="submit" class="btn btn-5">Add</button>
                    <a href="categories.php" class="btn btn-6">Cancel</a>
                </div>
            </form>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <?php include 'includes/modals.php'; ?>
    </body>
</html>

<script>
    document.getElementById('add-main-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('name').value.trim();
        if(!name){
            showAlertModal("Please enter a category name.");
            return;
        }
        fetch('<?= BASE_URL ?>admin/includes/check-duplicate-main.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `name=${encodeURIComponent(name)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                showAlertModal("This category already exists.", () => {});
            } else {
                showConfirmModal(
                    `Do you want to add "${name}" as a new main category?`,
                    () => {
                                fetch('<?= BASE_URL ?>admin/includes/add-main-category-handler.php', {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                    body: `name=${encodeURIComponent(name)}`
                                })
                                .then(res => res.text())
                                .then(data => {
                                    if(data === 'Success'){
                                        showAlertModal("Added successfully!", () => {
                                            window.location.reload();
                                        });
                                    } else {
                                        showAlertModal("Failed to add main category: " + data, () => {});
                                    }
                                })
                                .catch(err => showAlertModal("Error: " + err, () => {}));
                    },
                    () => {
                    }
                );
            }
        })
        .catch(err => showAlertModal("Error checking duplicate: " + err, () => {}));
    });

    function showConfirmModal(message, onYes, onNo) {
        const template = document.getElementById('confirmModal');
        const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
        document.body.appendChild(modal);
        modal.querySelector('p').textContent = message;
        modal.classList.add('show');
        const yesBtn = modal.querySelector('#confirmYes');
        const noBtn = modal.querySelector('#confirmNo');
        function cleanup() {
            yesBtn.removeEventListener('click', yesHandler);
            noBtn.removeEventListener('click', noHandler);
            modal.remove();
        }
        function yesHandler() {
            cleanup();
            if (typeof onYes === 'function') onYes();
        }
        function noHandler() {
            cleanup();
            if (typeof onNo === 'function') onNo();
        }
        yesBtn.addEventListener('click', yesHandler);
        noBtn.addEventListener('click', noHandler);
    }

    function showAlertModal(message, onOk){
        const template = document.getElementById('alertModal');
        const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
        document.body.appendChild(modal);
        modal.querySelector('p').textContent = message;
        modal.classList.add('show');
        const okBtn = modal.querySelector('#confirmOk');
        function cleanup() {
            okBtn.removeEventListener('click', okHandler);
            modal.remove();
        }
        function okHandler(){
            cleanup();
            if (typeof onOk === 'function'){ onOk()}
            else{};
        }
        okBtn.addEventListener('click', okHandler);
    }
</script>