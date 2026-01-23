<?php
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'account';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    //Fetch address info
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT *
        FROM user_addresses
        WHERE user_id = ? AND is_primary = 1
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    //State list
    $states = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ];
?>

<style>
    /* Form */
    .change-address-page .giant-container{
        padding: 50px 10% 200px 10%;
    }
    .change-address-page .section {
        border: 1px solid #5b5b5bff;
    }
    .change-address-page .section-header {
        position: relative;
        color: #3b3b3bff;
        background: #dededeff;
        padding: 15px;
        font-weight: 600;
        font-size: 1.3rem;
    }
    .change-address-page .section-content {
        border-top: 1px solid black;
        background: #ccccccff;
    }
    .change-address-page .contact-form {
        padding: 20px;
    }
    .change-address-page .row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .change-address-page .field {
        display: flex;
        flex-direction: column;
        color: #3b3b3b;
        font-weight: 600;
    }
    .change-address-page .field.big { flex: 6; }
    .change-address-page .field.small { flex: 4; }
    .change-address-page .field.huge { flex: 10; }
    .change-address-page .field input,
    .change-address-page .field select {
        width: 100%;
        padding: 10px;
        border: 1px solid #5b5b5bff;
        font-size: 1rem;
        background: white;
        border-radius: 0;
    }
    .change-address-page .field input:focus,
    .change-address-page .field select:focus {
        outline: none;
        border: 2px solid #000;
        box-shadow: none;
    }
    .change-address-page .button-row {
        margin-top: 15px;
        display: flex;
        gap: 15px;
    }

    @media (max-width: 768px) {
        .change-address-page .giant-container{
            padding: 50px 10% 90px 10%;
        }
    }
</style>

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
		<main class="change-address-page">
            <div class="giant-container">
                <div class="section">
                    <div class="section-header">
                        Change address
                    </div>
                    <div class="section-content">
                        <form id="changeAddressForm" method="POST">
                            <div class="contact-form">
                                <div class="row">
                                    <div class="field huge">
                                        <label>Full name</label>
                                        <input 
                                            type="text" 
                                            name="full_name"
                                            value="<?= htmlspecialchars($address['full_name'] ?? '') ?>"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="field huge">
                                        <label>Street address</label>
                                        <input
                                            type="text"
                                            name="street"
                                            value="<?= htmlspecialchars($address['street'] ?? '') ?>"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="field big">
                                        <label>City</label>
                                        <input 
                                            type="text" 
                                            name="city"
                                            value="<?= htmlspecialchars($address['city'] ?? '') ?>"
                                            required
                                        >
                                    </div>
                                    <div class="field small">
                                        <label>Zip code</label>
                                        <input 
                                            type="text" 
                                            name="zip"
                                            inputmode="numeric"
                                            pattern="[0-9]*"
                                            maxlength="5"
                                            value="<?= htmlspecialchars($address['zip'] ?? '') ?>"  
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="field huge">
                                        <label>State</label>
                                        <select name="state" required>
                                            <option value="">Select a state</option>
                                            <?php foreach ($states as $code => $name): ?>
                                                <option
                                                    value="<?= $code ?>"
                                                    <?= (($address['state'] ?? '') === $code) ? 'selected' : '' ?>
                                                >
                                                    <?= $name ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="button-row">
                                    <button class="btn btn-5">Save</button>
                                    <a href="my-profile.php" class="btn btn-6">Cancel</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>
    <?php 
        include '../includes/footer2.php';
        include '../includes/modals.php';
    ?>
    <script>
        // showAlertModal("Test alert.", () => {});
        // showConfirmModal(
        //     `Test confirm`,
        //     () => {
        //     },
        //     () => {
        //     }
        // );

        //Update address
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('changeAddressForm');
            if (!form) return;
            form.addEventListener('submit', (e) => {
                e.preventDefault(); 

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const formData = new FormData(form);

                fetch('<?= BASE_URL ?>actions/change-address.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlertModal('Address updated successfully.', () => {
                            window.location.href = 'my-profile.php';
                        });
                    } else {
                        showAlertModal(data.message || 'Something went wrong.', () => {});
                    }
                })
                .catch(err => {
                    console.error(err);
                    showAlertModal('Server error. Please try again.', () => {});
                });
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

