<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
	$currentPage = 'cart';
?>

<style>
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
        cursor: pointer;
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

            <div class="giant-container">
                <div class="section">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-check"></i>
                        1 - Contact information
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="contact-form">
                            <div class="row">
                                <div class="field big">
                                    <label>Email</label>
                                    <input type="text">
                                </div>

                                <div class="field small">
                                    <label>Phone</label>
                                    <input type="text">
                                </div>
                            </div>

                            <div class="button-row">
                                <a href="#" class="btn check-btn">Continue as guest</a>
                                <a href="#" class="btn btn-secondary me-2 my-btn-custom">Log in</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon"></i>
                        2 - Delivery address
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="contact-form">
                            <div class="row">
                                <div class="field huge">
                                    <label>Full name</label>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="row">
                                <div class="field huge">
                                    <label>Street address</label>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="row">
                                <div class="field big">
                                    <label>City</label>
                                    <input type="text">
                                </div>
                                <div class="field small">
                                    <label>Zip code</label>
                                    <input type="text" name="zip" inputmode="numeric" pattern="[0-9]*">
                                </div>
                            </div>
                            <div class="row">
                                <div class="field huge">
                                    <label>State</label>
                                    <select>
                                        <option value="">Select a state</option>
                                        <option value="AL">Alabama</option>
                                        <option value="AK">Alaska</option>
                                        <option value="AZ">Arizona</option>
                                        <option value="AR">Arkansas</option>
                                        <option value="CA">California</option>
                                        <option value="CO">Colorado</option>
                                        <option value="CT">Connecticut</option>
                                        <option value="DE">Delaware</option>
                                        <option value="FL">Florida</option>
                                        <option value="GA">Georgia</option>
                                        <option value="HI">Hawaii</option>
                                        <option value="ID">Idaho</option>
                                        <option value="IL">Illinois</option>
                                        <option value="IN">Indiana</option>
                                        <option value="IA">Iowa</option>
                                        <option value="KS">Kansas</option>
                                        <option value="KY">Kentucky</option>
                                        <option value="LA">Louisiana</option>
                                        <option value="ME">Maine</option>
                                        <option value="MD">Maryland</option>
                                        <option value="MA">Massachusetts</option>
                                        <option value="MI">Michigan</option>
                                        <option value="MN">Minnesota</option>
                                        <option value="MS">Mississippi</option>
                                        <option value="MO">Missouri</option>
                                        <option value="MT">Montana</option>
                                        <option value="NE">Nebraska</option>
                                        <option value="NV">Nevada</option>
                                        <option value="NH">New Hampshire</option>
                                        <option value="NJ">New Jersey</option>
                                        <option value="NM">New Mexico</option>
                                        <option value="NY">New York</option>
                                        <option value="NC">North Carolina</option>
                                        <option value="ND">North Dakota</option>
                                        <option value="OH">Ohio</option>
                                        <option value="OK">Oklahoma</option>
                                        <option value="OR">Oregon</option>
                                        <option value="PA">Pennsylvania</option>
                                        <option value="RI">Rhode Island</option>
                                        <option value="SC">South Carolina</option>
                                        <option value="SD">South Dakota</option>
                                        <option value="TN">Tennessee</option>
                                        <option value="TX">Texas</option>
                                        <option value="UT">Utah</option>
                                        <option value="VT">Vermont</option>
                                        <option value="VA">Virginia</option>
                                        <option value="WA">Washington</option>
                                        <option value="WV">West Virginia</option>
                                        <option value="WI">Wisconsin</option>
                                        <option value="WY">Wyoming</option>
                                    </select>
                                </div>
                            </div>

                            <div class="button-row">
                                <a href="#" class="btn check-btn">Continue</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon"></i>
                        3 - Shipping method
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="option-group">
                            <label class="option">
                                <input type="radio" name="choice" value="option1">
                                <span class="circle"></span>
                                UPS 2nd Day Air
                                <span class="price">-</span>
                                <span class="price">$50.99</span>
                            </label>

                            <label class="option">
                                <input type="radio" name="choice" value="option2">
                                <span class="circle"></span>
                                UPS 3 Day Select
                                <span class="price">-</span>
                                <span class="price">$25.99</span>
                            </label>

                            <label class="option">
                                <input type="radio" name="choice" value="option3">
                                <span class="circle"></span>
                                UPS Ground
                                <span class="price">-</span>
                                <span class="price">$15.99</span>
                            </label>
                            <div class="button-row">
                                <a href="#" class="btn check-btn">Continue</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        <i class="fa-solid fa-circle-minus neutral-icon"></i>
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
                                <a href="success.php" class="btn check-btn">Confirm and pay</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <?php 
        include '../includes/footer.php'
        ?>
        <script>
			document.querySelectorAll('.section-header').forEach(header => {
                header.addEventListener('click', () => {
                    const section = header.parentElement;
                    const content = section.querySelector('.section-content');
                    const chevron = header.querySelector('.chevron');

                    const isOpen = section.classList.toggle('open');

                    if (isOpen) {
                        content.style.maxHeight = content.scrollHeight + "px";
                    } else {
                        content.style.maxHeight = "0px";
                    }
                });
            });

            /*Limit zip code input*/
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
		</script>
    </body>
</html>