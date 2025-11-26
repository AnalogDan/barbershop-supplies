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
	}

    .contact-form {
        padding: 20px;
    }
    .row {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
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
                    <div class="section-header">1 - Contact information<span class="chevron">&rsaquo;</span></div>
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
                    <div class="section-header">2 - Delivery address<span class="chevron">&rsaquo;</span></div>
                    <div class="section-content">
                        ola bo
                        laa
                        bola
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">3 - Shipping method<span class="chevron">&rsaquo;</span></div>
                    <div class="section-content">
                        ola
                        bo
                        la
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">4 - Payment method<span class="chevron">&rsaquo;</span></div>
                    <div class="section-content">
                        ola bo
                        laa
                        bola
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
		</script>
    </body>
</html>