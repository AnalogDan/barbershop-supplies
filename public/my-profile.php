<?php
    session_start();

    // if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    //     header("Location: login.php");
    //     exit;
    // }
    require_once __DIR__ . '/../includes/db.php';
	$currentPage = 'account';
?>

<style>
    /*Main structure*/
    .giant-container{
        padding: 50px 10% 2rem 10%;
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
    }
    /* .section.open .section-content {
        max-height: 600px;   
    } */
    
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

    /*User info*/
    .user-info{
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1rem
    }
    .user-change{
        display: block;
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1.3rem;
        margin-bottom: 1.3rem;
        text-decoration: underline;
    }


    /*My favorites*/
    .favs{
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
     .item-row{
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin: 0.7rem auto;
        height: 5rem;
        gap: 1rem; 
        display: flex;
        align-items: center;  
    }
    .item-row img{
        height: 6.5rem;
        width: auto;
    }
    .item-name{
        width: 15rem;
    }

	.item-row i {
	cursor: pointer;
	font-size: 20px;
	transition: color 0.2s ease, transform 0.2s ease;
	}
	.item-row i:hover {
	color: black;
	transform: scale(1.15);
	}
    .btn{
        border-radius: 13px !important;
        display: block;
        margin: 0 auto 1rem auto;
        width: auto;
    }
    .spacer{
        height: 0.1rem;
        width: 100px;
        flex-shrink: 0;
    }
    
    /*Order history*/
    .order{
        width: auto;
        display: inline-block;
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1.3rem;
        /* margin-bottom: 1.3rem; */
        text-decoration: underline;
    }
    .order-end{
        width: auto;
        display: inline-block;
        color: #3b3b3bff;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 2.5rem;
        margin-top: 1.3rem;
        margin-bottom: 1.3rem;
        text-decoration: underline;
    }

    .log-out{
        display: inline-block;
        margin: 0 auto 5rem auto;
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
								<h1>My profile</h1>
								<p class="mb-4">Manage your account details, orders and favorites.</p>
							</div>
						</div>
					</div>
				</div>
			</div>

            <div class="giant-container">
                <div class="section">
                    <div class="section-header">
                        User information
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                       <div class="user-info">
                            Name: John Doe
                        </div>
                        <div class="user-info">
                            Email: johndoe@gmail.com
                        </div>
                        <a href="#" class="user-change">Change password</a> 
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        My favorites
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <div class="favs">
                            <div class="item-row">
                                <span><img src="images/products/thumb_1756950792_1b6a822b.png"></span>
                                <span class="item-name">Andis Slimline Pro Chrome Trimmer</span>
                                <span class="trash"><i class="fa-solid fa-trash"></i></span>
                            </div>
                            <div class="item-row">
                                <span><img src="images/products/thumb_1756950792_1b6a822b.png"></span>
                                <span class="item-name">Andis Slimline Pro Chrome Trimmer</span>
                                <span class="trash"><i class="fa-solid fa-trash"></i></span>
                            </div>
                            <div class="item-row">
                                <span><img src="images/products/thumb_1756950792_1b6a822b.png"></span>
                                <span class="item-name">Andis Slimline Pro Chrome Trimmer</span>
                                <span class="trash"><i class="fa-solid fa-trash"></i></span>
                            </div>
                            <a href="#" class="btn check-btn">See all</a>
                            <div class="spacer"></div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        Order history
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                        <a href="#" class="order">Order #1090 - Delivered</a><br>
                        <a href="#" class="order">Order #1091 - Delivered</a><br>
                        <a href="#" class="order">Order #1098 - In transit</a><br> 
                        <a href="my-orders.php" class="order-end">See all</a> 
                    </div>
                </div>
            </div>

            <div class="log-out">
                <a href="#" class="btn btn-secondary me-2 my-btn-custom">Log out</a>
            </div>
        </main>
        <?php 
        include '../includes/footer2.php'
        ?>
        <script>
            /*Expand dropdowns*/
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