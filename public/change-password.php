<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'account';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
?>

<style>
    .product-form{
        max-width: 500px;
        width: 100%;
        margin: 40px auto 70px auto;
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

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
		<main>
            <form class="product-form" method="POST">
                <div class="section-title">
                    <h2>Change password</h2>
                </div>
                <!-- Old password -->
                <div class="password-wrapper" style="position: relative;">
                    <label for="old_password"><strong>Old password</strong></label>
                    <div class="input-with-icon">
                        <input
                            id="old_password"
                            type="password"
                            name="old_password"
                            required
                        >
                        <span class="password-toggle" data-target="old_password">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- New password -->
                <div class="password-wrapper" style="position: relative;">
                    <label for="new_password"><strong>New password</strong></label>
                    <div class="input-with-icon">
                        <input
                            id="new_password"
                            type="password"
                            name="new_password"
                            required
                        >
                        <span class="password-toggle" data-target="new_password">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="button-row">
                    <button type="submit" class="btn btn-5">Add</button>
                    <a href="categories.php" class="btn btn-6">Cancel</a>
                </div>
            </form>
        </main>
    <?php 
        include '../includes/footer2.php';
        include '../includes/modals.php';
    ?>
    <script src="/barbershopSupplies/public/js/password-toggle-multi.js"></script>
    <script>
        // showAlertModal("Test alert.", () => {});
        // showConfirmModal(
        //     `Test confirm`,
        //     () => {
        //     },
        //     () => {
        //     }
        // );

        //Fetch check and change password
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.product-form');
            if (!form) return;
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const oldPassword = document.getElementById('old_password').value;
                const newPassword = document.getElementById('new_password').value;

                const checkRes = await fetch('/barbershopSupplies/actions/check-old-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ old_password: oldPassword })
                });

                const checkData = await checkRes.json();

                if (!checkData.success) {
                    showAlertModal(
                        'Old password is incorrect.',
                        () => {}
                    );
                    return;
                }

                showConfirmModal(
                    'Are you sure you want to change your password?',
                    async () => {
                        const changeRes = await fetch('/barbershopSupplies/actions/change-password.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ new_password: newPassword })
                        });

                        const changeData = await changeRes.json();

                        if (changeData.success) {
                            showAlertModal('Password changed successfully.', () => {
                                window.location.href = 'my-profile.php';
                            });
                        } else {
                            showAlertModal(changeData.message || 'Error changing password.', () => {});
                        }
                    },
                    () => {
                        // Cancel — do nothing
                    }
                );
            });
        });

        //Modal functions
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
    </body>
    
</html>

