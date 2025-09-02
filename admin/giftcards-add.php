<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
	require_once __DIR__ . '/../includes/db.php';
?>
<!DOCTYPE html>
<style>
    .product-form{
        max-width: 500px;
        width: 100%;
        margin: 40px auto 140px auto;
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
            <div class="section-title">
                <h2>Add sub category</h2>
            </div>
            <form class="product-form" id="giftcard-form">
                <label for="letters">Code</label>
                <div class="side-by-side">
                    <input type="text" id="letters" name="letters" placeholder="ABCDEF" maxlength="6" pattern="[A-Z]{6}" title="6 capital letters only">
                    <input type="text" id="numbers" name="numbers" placeholder="1234" maxlength="4" pattern="\d{4}" title="4 digits only">
                </div>
                <label for="value">Value</label>
                <!-- The database allowed values are: CHECK (value IN (50, 100, 150, 200, 250, 300)); To drop constraint do DROP CONSTRAINT chk_giftcard_value; -->
                <select id="value" name="value">
                    <option value="">Select a value</option>
                    <option value="50">$50</option>
                    <option value="100">$100</option>
                    <option value="150">$150</option>
                    <option value="200">$250</option>
                    <option value="300">$300</option>
                </select>
                <div class="button-row">
                    <button type="submit" class="btn btn-5">Add</button>
                    <a href="giftcards.php" class="btn btn-6">Cancel</a>
                </div>
            </form>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <?php include 'includes/modals.php'; ?>


        <script>
            document.getElementById('letters').addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z]/g,'');
            });
            document.getElementById('numbers').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
            document.getElementById('giftcard-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                const letters = formData.get('letters').trim();
                const numbers = formData.get('numbers').trim();
                const value   = formData.get('value');
                const lettersRegex = /^[A-Z]{6}$/;
                const numbersRegex = /^\d{4}$/;
                if (!lettersRegex.test(letters)) {
                    alert("Code must have exactly 6 capital letters.");
                    return;
                }
                if (!numbersRegex.test(numbers)) {
                    alert("Code must have exactly 4 numbers.");
                    return;
                }
                if (!value) { 
                    alert("Please select a gift card value.");
                    return;
                }
                showConfirmModal(
                    "Add gift card?",
                    () => {
                        fetch('includes/giftcards-add-handler.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlertModal("Gift card added!", () => { this.reset(); });
                            } else {
                                showAlertModal("Error: " + data.message, () => {});
                            }
                        })
                        .catch(error => {
                            showAlertModal("Something went wrong! " + error, () => {});
                        });
                    },
                    () => {}     
                );
                   
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
    </body>
</html>