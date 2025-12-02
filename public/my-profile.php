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
                       
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        My favorites
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        Order history
                        <span class="chevron">&rsaquo;</span>
                    </div>
                    <div class="section-content">
                    </div>
                </div>
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