<?php
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/header.php';
	$currentPage = 'cart';

    require_once __DIR__ . '/../includes/pricing.php';
	$tz = new DateTimeZone('America/Los_Angeles');
    $checkoutSteps = $_SESSION['checkout']['steps'] ?? []; 
    

	//Fetch cart/product info
	$cartItems = [];
	if (!empty($_SESSION['cart_id'])) {
		$cartId = (int) $_SESSION['cart_id'];

		$stmt = $pdo->prepare("
			SELECT
				ci.product_id,
				ci.quantity,
				p.name,
				p.price,
				p.stock,
				p.cutout_image,
				p.sale_price,
				p.sale_start,
				p.sale_end
			FROM cart_items ci
			JOIN products p ON p.id = ci.product_id
			WHERE ci.cart_id = ?
		");
		$stmt->execute([$cartId]);
		$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	//Correct for stock changes
	if (!empty($cartItems)) {
		foreach ($cartItems as $item) {
			$productId = (int) $item['product_id'];
			$stock     = (int) $item['stock'];
			$quantity  = (int) $item['quantity'];
			if ($stock <= 0) {
				$stmt = $pdo->prepare("
					DELETE FROM cart_items
					WHERE cart_id = ? AND product_id = ?
				");
				$stmt->execute([$cartId, $productId]);
			} elseif ($quantity > $stock) {
				$stmt = $pdo->prepare("
					UPDATE cart_items
					SET quantity = ?
					WHERE cart_id = ? AND product_id = ?
				");
				$stmt->execute([$stock, $cartId, $productId]);
			}
		}
		$stmt = $pdo->prepare("
				SELECT
					ci.product_id,
					ci.quantity,
					p.name,
					p.price,
					p.stock,
					p.cutout_image,
					p.sale_price,
					p.sale_start,
					p.sale_end
				FROM cart_items ci
				JOIN products p ON p.id = ci.product_id
				WHERE ci.cart_id = ?
			");
			$stmt->execute([$cartId]);
			$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

    //Derive effective sale prices and have a final items list
    $summaryItems = [];
    foreach ($cartItems as $item) {
        $pricing = getProductPricing($item, $tz);
        $unitPrice = $pricing['final_price'];
        $quantity  = (int) $item['quantity'];

        $summaryItems[] = [
            'product_id'        => (int) $item['product_id'],
            'name'              => $item['name'],
            'cutout_image'      => $item['cutout_image'],
            'quantity'          => $quantity,
            'unit_price'        => $unitPrice,
            'line_total'        => $unitPrice * $quantity,
            'is_on_sale'        => $pricing['is_on_sale'],
            'original_price'   => $pricing['original_price'],
            'discount_percent' => $pricing['discount_percent'],
        ];
    }
    $subtotal   = 0;
    $itemCount = 0;
    foreach ($summaryItems as $item) {
        $subtotal   += $item['line_total'];
        $itemCount += $item['quantity'];
    }
    $taxRate = 0.0925;
    $taxableAmount = $subtotal;
    $salesTax = round($taxableAmount * $taxRate, 2);
    // Shipping will be calculated later
    $shipping = null;
    $total = $subtotal + $salesTax + ($shipping ?? 0);

    //Fetch user contact data 
    $isLoggedIn = false;
    $userEmail = '';
    $userPhone = '';
    if (!empty($_SESSION['user_id'])) {
        $isLoggedIn = true;
        $stmt = $pdo->prepare("
            SELECT email, phone
            FROM users
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userEmail = $user['email'] ?? '';
            $userPhone = $user['phone'] ?? '';
        }
    }

    //Fetch user address data 
    $address = [
        'full_name' => '',
        'street'    => '',
        'city'      => '',
        'state'     => '',
        'zip'       => ''
    ];
    $country = 'United States';
    if (!empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("
            SELECT full_name, street, city, state, zip
            FROM user_addresses
            WHERE user_id = ? AND is_primary = 1
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $dbAddress = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dbAddress) {
            $address = array_merge($address, $dbAddress);
        }
    }
    $step1 = $checkoutSteps[1] ?? [];
    $step2 = $checkoutSteps[2] ?? [];
    $email = $step1['email'] ?? $userEmail ?? '';
    $phone = $step1['phone'] ?? $userPhone ?? '';
    $fullName = $step2['full_name'] ?? $address['full_name'] ?? '';
    $street   = $step2['street']    ?? $address['street']    ?? '';
    $city     = $step2['city']      ?? $address['city']      ?? '';
    $state    = $step2['state']     ?? $address['state']     ?? '';
    $zip      = $step2['zip']       ?? $address['zip']       ?? '';
    $hasAddress = !empty($fullName)
    || !empty($street)
    || !empty($city)
    || !empty($state)
    || !empty($zip);
?>

<style>
    /*Order summary*/
    .order-summary {
        border: 1px solid #5b5b5bff;
        background: #dededeff;
        padding: 1.5rem;
        width: 80%;
        margin: 3rem auto 0 auto;
    }
    .order-summary h3 {
        font-size: 1.4rem;
        font-weight: 600;
        color: #3b3b3bff;
        margin-bottom: 1.2rem;
    }
    .summary-items {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .summary-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 600;
        color: #3b3b3bff;
    }
    .summary-item img {
        height: 4.5rem;
        width: auto;
    }
    .summary-item-name {
        flex: 1;
    }
    .summary-totals {
        margin-top: 1.5rem;
        border-top: 1px solid #5b5b5bff;
        padding-top: 1rem;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.6rem;
    }
    .summary-total {
        font-size: 1.2rem;
    }
    /*Image summary*/
    .summary-item-image {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .summary-item-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    /*Price sale */
    .price-original {
        color: #000;
        opacity: 0.45;
        text-decoration: line-through;
        margin-right: 10px;
        white-space: nowrap;
    }
    /*Address summary */
    .summary-address {
        margin-top: 1.2rem;
        padding: 1rem 0;
        border-top: 1px solid #5b5b5bff;
        color: #3b3b3bff;
        font-weight: 500;
    }
    .summary-address-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        margin-bottom: 0.6rem;
    }
    .summary-address-header span {
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .summary-address-content {
        font-size: 0.9rem;
        line-height: 1.4;
        color: #4a4a4aff;
    }

    /*Main structure*/
    .giant-container{
        padding: 50px 10% 200px 10%;
    }
	.section {
        border: 1px solid #5b5b5bff;
        border-bottom: 0;
    }
    .section-header {
        position: relative;
        color: #3b3b3bff;
        background: #dededeff;
        padding: 15px;
        font-weight: 600;
        font-size: 1.3rem;
        cursor: default;
    }
    .neutral-icon{
        opacity: 0.6;
        margin-right: 0.6rem;
    }
    .fa-circle-check {
        opacity: 1;
        margin-right: 0.6rem;
    }
    .section-content {
        border-top: 1px solid black;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.9s ease;
        background: #ccccccff;
        
        white-space: pre-line;
    }
    .section.open .section-content {
        max-height: 300px;   
    }
    
    .section:nth-child(odd) .section-header {
        background: #dedede;   
    }
    .section:nth-child(even) .section-header {
        background: #cccccc;  
    }
    .section:nth-child(odd) .section-content {
        background: #dedede;
    }
    .section:nth-child(even) .section-content {
        background: #cccccc;
    }
    .section:last-child .section-content {
        border-bottom: 1px solid #5b5b5bff;;
    }


    .chevron {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%) rotate(0deg);
        font-size: 1.9rem;
        color: #333;
        transition: transform 0.25s ease;
        pointer-events: none;
    }
    .section.open .chevron {
        transform: translateY(-50%) rotate(90deg);
    }

    /*Contact info*/
    .my-btn-custom {
    border-radius: 10px !important;
    }
    .btn {
		border-radius: 10px !important;  
        margin-bottom: 15px;
	}

    .contact-form {
        padding: 20px;
    }
    .row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .field {
        display: flex;
        flex-direction: column;
        color: #3b3b3b;
        font-weight: 600;
    }
    .field.big { 
        font-size: 1rem;
        flex: 6; 
    }    
    .field.small { 
        font-size: 1rem;
        flex: 4; 
    }  

    .field input {
        width: 100%;
        padding: 10px;
        border: 1px solid #5b5b5bff;
        font-size: 1rem;
        background: white;
        border-radius: 0;
    }
    .field input:focus {
        outline: none;
        border: 2px solid #000;
        box-shadow: none;
    }

    .button-row {
        margin-top: 10px;
        display: flex;
        gap: 15px;
    }

    /*Delivery address*/
    .field.huge{
        font-size: 1rem;
        flex: 10;
    }
    .field select {
        width: 100%;
        padding: 10px;
        border: 1px solid #5b5b5bff;
        font-size: 1rem;
        background: white;
        border-radius: 0;
    }
    .field select:focus {
        outline: none;
        border: 2px solid #000;
        box-shadow: none;
    }

    /*Shipping method */
    .option-group {
        padding: 0 20px 20px 20px;
        display: flex;
        flex-direction: column;
    }
    .option-group > :last-child {
        margin-top: 20px;
    }
    .option {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        color: #3b3b3b;
    }
    .option input {
        display: none; 
    }
    .circle {
        background: white;
        width: 20px;
        height: 20px;
        border: 1px solid #333;
        border-radius: 50%;
        margin-right: 20px;
        margin-left: 10px;
        margin-bottom: -26px;
        position: relative;
        transition: all 0.2s ease;
    }
    .option input:checked + .circle {
        border: 6px solid #c6be6aff;
    }
    .price {
        margin-bottom: -26px;
        margin-left: 40px; 
        font-weight: 600;
        color: #3b3b3b;
    }

    /*Payment method*/
    .payment-method{
        padding: 20px 0 15px 20px;
    }
    .three-section-bar {
        width: 80%;
        height: 60px;          
        display: flex;
    }
    .sec1 { 
        display: flex;
        justify-content: center;  
        align-items: center;
        flex: 0 0 10%; 
    }
    .sec2 { 
        display: flex;
        flex-direction: column;
        justify-content: center; 
        padding-left: 15px; 
        flex: 0 0 auto; 
    }
    .sec3 { 
        display: flex;
        justify-content: center; 
        align-items: center;
        flex: 0 0 30%; 
    }
    .circle-sec1 {
        margin: 0;
    }

    .sec2-text .main {
        margin-bottom: -10px;
        font-size: 1rem;
        font-weight: 600;
        color: #3b3b3b;
    }

    .sec2-text .sub {
        font-size: 0.8rem;
        font-weight: 600;
        color: #777;    
    }

    .pay-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 75px;
        height: auto;
        margin: 0; 
    }
    .pay-icon-small {
        width: 40px;
        height: auto;
    }
    .pay-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;  
    }

    /*Cursor*/
    .section.active .section-header,
    .section.completed .section-header {
        cursor: pointer;
    }
    .section.locked .section-header {
        cursor: default;
        opacity: 0.75;
    }
    /*Locked sections */
    .section.locked {
        opacity: 0.5;
    }

    


</style>

<!DOCTYPE html>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
		<main>
            <div class="hero">
				<div class="container">
					<div class="row justify-content-between">
						<div class="col-lg-5">
							<div class="intro-excerpt">
								<h1>Checkout</h1>
								<p class="mb-4">Securely complete your purchase in a few simple steps.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

            <div class="order-summary">
                <h3>Order summary</h3>
                <div class="summary-items">
                    <?php foreach ($summaryItems as $item): ?>
                        <div class="summary-item">
                            <div class="summary-item-qty">
                                ×<?= (int) $item['quantity'] ?>
                            </div>
                            <div class="summary-item-image">
                                <img src="<?= htmlspecialchars($item['cutout_image']) ?>" alt="">
                            </div>
                            <div class="summary-item-name">
                                <?= htmlspecialchars($item['name']) ?>
                            </div>
                            <div class="summary-item-price">
                                <?php if ($item['is_on_sale']): ?>
                                    <span class="price-original">
                                        $<?= number_format($item['original_price'] * $item['quantity'], 2) ?>
                                    </span>
                                    <span class="price-sale">
                                        $<?= number_format($item['line_total'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="price-regular">
                                        $<?= number_format($item['line_total'], 2) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-totals">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Sales tax</span>
                        <span>$<?= number_format($salesTax, 2) ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping</span>
                        <span>
                            <?= $shipping === null
                                ? 'Calculated at next step'
                                : '$' . number_format($shipping, 2)
                            ?>
                        </span>
                    </div>
                    <div class="summary-line summary-total">
                        <span>Total</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>

                <div class="summary-address">
                    <div class="summary-address-content">
                        <strong>Address:</strong>
                        <span id="summary-address">
                            <?php if ($hasAddress): ?>
                                <?= htmlspecialchars(trim(implode(', ', array_filter([
                                    $fullName,
                                    $street,
                                    $city,
                                    $state . ($zip ? " $zip" : ''),
                                    $country
                                ])))) ?>
                            <?php else: ?>
                                No address yet
                            <?php endif; ?>
                        </span>
                        <br><br>

                        <strong>Contact information:</strong>
                        <span id="summary-contact">
                            <?= htmlspecialchars(trim(implode(', ', array_filter([
                                $email,
                                $phone
                            ])))) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="giant-container">
                <div class="section active open" data-step="1" id="step-1">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon step-icon"></i>
                        1 - Contact information
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="contact-form">
                            <div class="row">
                                <div class="field big">
                                    <label>Email</label>
                                    <input type="text"
                                        name="email"
                                        id="checkout-email"
                                        value="<?= htmlspecialchars(
                                            $checkoutSteps[1]['email']
                                            ?? $userEmail
                                            ?? ''
                                        ) ?>"
                                    >
                                </div>
                                <div class="field small">
                                    <label>Phone</label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*"
                                        name="phone"
                                        id="checkout-phone"
                                        value="<?= htmlspecialchars(
                                            $checkoutSteps[1]['phone']
                                            ?? $userPhone
                                            ?? ''
                                        ) ?>"
                                    >
                                </div>
                            </div>

                            <div class="button-row">
                                <?php if (!$isLoggedIn): ?>
                                    <a href="#" class="btn check-btn step-continue">Continue as guest</a>
                                    <a href="#" class="btn btn-secondary me-2 my-btn-custom">Log in</a>
                                <?php else: ?>
                                    <a href="#" class="btn check-btn step-continue">Continue</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section locked" data-step="2" id="step-2">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon step-icon"></i>
                        2 - Delivery address
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="contact-form">
                            <div class="row">
                                <div class="field huge">
                                    <label>Full name</label>
                                    <input type="text" name="full_name" id="checkout-fullname"
                                        value="<?= htmlspecialchars(
                                            $checkoutSteps[2]['full_name']
                                            ?? $address['full_name']
                                            ?? ''
                                        ) ?>"
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="field huge">
                                    <label>Street address</label>
                                    <input type="text" name="street" id="checkout-street"
                                        value="<?= htmlspecialchars(
                                            $checkoutSteps[2]['street']
                                            ?? $address['street']
                                            ?? ''
                                        ) ?>"
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="field big">
                                    <label>City</label>
                                    <input type="text" name="city" id="checkout-city"
                                        value="<?= htmlspecialchars(
                                            $checkoutSteps[2]['city']
                                            ?? $address['city']
                                            ?? ''
                                        ) ?>"
                                    >
                                </div>
                                <div class="field small">
                                    <label>Zip code</label>
                                    <input type="text" name="zip" inputmode="numeric" pattern="[0-9]*" id="checkout-zip"
                                        value="<?= htmlspecialchars(
                                            $checkoutSteps[2]['zip']
                                            ?? $address['zip']
                                            ?? ''
                                        ) ?>"
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="field huge">
                                    <label>State</label>
                                    <select name="state" id="checkout-state">
                                        <option value="">Select a state</option>
                                        <?php
                                        $selectedState =
                                            $checkoutSteps[2]['state']
                                            ?? $address['state']
                                            ?? '';
                                        foreach ([
                                            'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas',
                                            'CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware',
                                            'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois',
                                            'IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana',
                                            'ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan',
                                            'MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana',
                                            'NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey',
                                            'NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota',
                                            'OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania',
                                            'RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota',
                                            'TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont',
                                            'VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia',
                                            'WI'=>'Wisconsin','WY'=>'Wyoming'
                                        ] as $code => $name):
                                        ?>
                                            <option value="<?= $code ?>" <?= $code === $selectedState ? 'selected' : '' ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="button-row">
                                <a href="#" class="btn check-btn step-continue">Continue</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section locked" data-step="3">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon step-icon"></i>
                        3 - Shipping method
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="option-group">
                            <label class="option">
                                <input type="radio" name="2nd_day" value="option1">
                                <span class="circle"></span>
                                UPS 2nd Day Air
                                <span class="price">-</span>
                                <span class="price">$50.99</span>
                            </label>

                            <label class="option">
                                <input type="radio" name="3_day" value="option2">
                                <span class="circle"></span>
                                UPS 3 Day Select
                                <span class="price">-</span>
                                <span class="price">$25.99</span>
                            </label>

                            <label class="option">
                                <input type="radio" name="ground" value="option3">
                                <span class="circle"></span>
                                UPS Ground
                                <span class="price">-</span>
                                <span class="price">$15.99</span>
                            </label>
                            <div class="button-row">
                                <a href="#" class="btn check-btn step-continue">Continue</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section locked" data-step="4">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon step-icon"></i>
                        4 - Payment method
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="payment-method">
                            <div class="three-section-bar">
                                <div class="sec1">
                                    <label class="option">
                                        <input type="radio" name="select1">
                                        <span class="circle circle-sec1"></span>
                                    </label>
                                </div>
                                <div class="sec2">
                                    <div class="sec2-text">
                                        <div class="main">Credit/Debit card</div>
                                        <div class="sub">You'll be redirected to Stripe to complete your payment.</div>
                                    </div>
                                </div>
                                <div class="sec3">
                                    <span class="pay-icon pay-icon-small">
                                        <img src="images/cards.png" alt="Card">
                                    </span>
                                </div>
                            </div>
                            <div class="three-section-bar">
                                <div class="sec1">
                                    <label class="option">
                                        <input type="radio" name="select1">
                                        <span class="circle circle-sec1"></span>
                                    </label>
                                </div>
                                <div class="sec2">
                                    <div class="sec2-text">
                                        <div class="main">PayPal</div>
                                        <div class="sub">Secure payment trough your PayPal account.</div>
                                    </div>
                                </div>
                                <div class="sec3">
                                    <span class="pay-icon">
                                        <img src="images/paypal.png" alt="PayPal">
                                    </span>
                                </div>
                            </div>
                            <div class="button-row">
                                <a href="success.php" class="btn check-btn step-continue">Confirm and pay</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <?php 
        include '../includes/footer.php';
        include '../includes/modals.php'
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
            let currentStep = 1;

            //Function when completing a step UI changes
            function completeStep(section, step, nextStep) {
                const content = section.querySelector('.section-content');
                const icon = section.querySelector('.step-icon');
                section.classList.remove('open', 'active');
                section.classList.add('completed');
                icon.classList.remove('fa-circle-minus', 'neutral-icon');
                icon.classList.add('fa-circle-check');
                content.style.maxHeight = '0px';

                currentStep = nextStep;
                const nextSection = document.querySelector(`.section[data-step="${nextStep}"]`);
                if (!nextSection) return;
                const nextContent = nextSection.querySelector('.section-content');
                nextSection.classList.remove('locked');
                nextSection.classList.add('open', 'active');
                nextContent.style.maxHeight = nextContent.scrollHeight + 'px';
            }

            //Function when editing completed step
            function invalidateFutureSteps(fromStep) {
                document.querySelectorAll('.section').forEach(section => {
                    const step = parseInt(section.dataset.step, 10);
                    if (step > fromStep) {
                        section.classList.remove('active', 'completed');
                        section.classList.add('locked');
                        const icon = section.querySelector('.step-icon');
                        if (icon) {
                            icon.classList.remove('fa-circle-check');
                            icon.classList.add('fa-circle-minus', 'neutral-icon');
                        }
                        const content = section.querySelector('.section-content');
                        section.classList.remove('open');
                        content.style.maxHeight = '0px';
                    }
                });
            }

            //Function to update summary contact and address
            function updateSummary() {
                const fullName = document.getElementById('checkout-fullname')?.value.trim() || '';
                const street   = document.getElementById('checkout-street')?.value.trim() || '';
                const city     = document.getElementById('checkout-city')?.value.trim() || '';
                const state    = document.getElementById('checkout-state')?.value.trim() || '';
                const zip      = document.getElementById('checkout-zip')?.value.trim() || '';
                const country  = 'United States';
                const email = document.getElementById('checkout-email')?.value.trim() || '';
                const phone = document.getElementById('checkout-phone')?.value.trim() || '';

                const addressParts = [fullName, street, city, state + (zip ? " " + zip : ''), country].filter(Boolean);
                document.getElementById('summary-address').textContent = addressParts.length ? addressParts.join(', ') : 'No address yet';
                const contactParts = [email, phone].filter(Boolean);
                document.getElementById('summary-contact').textContent = contactParts.join(', ');
            }

            //Listener changing summary dinamically
            ['checkout-email','checkout-phone','checkout-fullname','checkout-street','checkout-city','checkout-state','checkout-zip'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', updateSummary);
                }
            });
            updateSummary();
          

            //Handle continue button 
            const stepFieldMap = {
                1: ['email', 'phone'],
                2: ['full_name', 'street', 'city', 'zip', 'state']
            };
            document.querySelectorAll('.step-continue').forEach(button => {
                button.addEventListener('click', async e => {
                    e.preventDefault();
                    const section = button.closest('.section');
                    const step = parseInt(section.dataset.step, 10);
                    const nextStep = step + 1;
                    const fields = stepFieldMap[step] || [];
                    const stepData = {};
                    fields.forEach(name => {
                        const input = section.querySelector(`[name="${name}"]`);
                        stepData[name] = input ? input.value.trim() : null;
                    });
                    const payload = {
                        step: step,
                        data: stepData
                    };
                    // Validate/store in session before completing the step
                     try {
                        const response = await fetch('/barbershopSupplies/actions/checkout-continue.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                        const result = await response.json();
                        console.log('Checkout continue response:', result);
                        // console.log('STEP:', step);
                        // console.log('STEP DATA:', stepData);
                        // console.log('PAYLOAD:', payload);
                        if (!result.success) {
                            showAlertModal(
                                result.message || 'Validation error',
                                () => {}
                            );
                            return;
                        }
                        completeStep(section, step, nextStep);
                    } catch (err) {
                        console.error(err);
                        showAlertModal(
                            'Network error. Please try again.',
                            () => {}
                        );
                    }
  
                });
            });
            

            //Clicking any open-able section 
            document.querySelectorAll('.section-header').forEach(header => {
                header.addEventListener('click', () => {
                    const section = header.parentElement;
                    const step = parseInt(section.dataset.step, 10);
                    const wasCompleted = section.classList.contains('completed');
                    if (step > currentStep) return;
                    const content = section.querySelector('.section-content');
                    if (wasCompleted) {
                        invalidateFutureSteps(step);
                        currentStep = step;
                        section.classList.remove('completed', 'locked');
                        section.classList.add('active');
                        section.classList.add('open');
                        content.style.maxHeight = content.scrollHeight + 'px';
                        return;
                    }
                    const isOpen = section.classList.toggle('open');
                    content.style.maxHeight = isOpen
                        ? content.scrollHeight + 'px'
                        : '0px';
                });
            });
            

            /*Limit zip code and phone numeric input*/
            document.addEventListener("DOMContentLoaded", function () {
                const zip = document.querySelector('input[name="zip"]');

                if (zip) {
                    zip.addEventListener("input", function () {
                        this.value = this.value.replace(/\D/g, "");

                        if (this.value.length > 5) {
                            this.value = this.value.slice(0, 5);
                        }
                    });
                }
            });
            document.addEventListener("DOMContentLoaded", function () {
                const phone = document.querySelector('input[name="phone"]');

                if (phone) {
                    phone.addEventListener("input", function () {
                        this.value = this.value.replace(/\D/g, "");
                    });
                }
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