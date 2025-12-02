<?php
	require_once __DIR__ . '/../includes/db.php';
	session_start();
	$error = $_SESSION['login_error'] ?? null;
	unset($_SESSION['login_error']);
	$currentPage = 'account';

	//echo password_hash("barberThings", PASSWORD_DEFAULT);
	// username: barberAdmin
?> 
<!DOCTYPE html>
<style>
	.login-links {
		text-align: left; 
	}
	.login-links a { 
		font-weight: 600;  
		color: #666;          
	}
	.login-links a:hover {
		color: #333;           
	}

	.btn{
		width: fit-content !important;
	}
</style>
<html lang="en">
	<?php include '../includes/head.php'; ?>
    <?php include '../includes/navbar.php'; ?>
	<body>
		<?php if ($error): ?>
			<div class="alert-container">
				<div class="alert alert-danger text-center rounded" role="alert">
					<?= htmlspecialchars(($error)) ?>
				</div>
			</div>
		<?php endif; ?>
		<main>
		<div class="section-title">
				<h2>Create account</h2>
				<img src="/barbershopSupplies/public/images/Ornament1.png" alt="Ornament">
		</div>
		<form class="admin-login-form" action="/barbershopSupplies/includes/process-login.php" method="POST">
			<div>
				<label for="first-name"><strong>First name</strong></label>
				<input id="first-name" type="text" name="first-name" required>
			</div>
			<div>
				<label for="last-name"><strong>Last name</strong></label>
				<input id="last-name" type="text" name="last-name" required>
			</div>
			<div>
				<label for="email"><strong>Email</strong></label>
				<input id="email" type="text" name="email" required>
			</div>
			<div class="password-wrapper" style="position: relative; max-width: 400px;">
				<label for="password"><strong>Password</strong></label>
				<div class="input-with-icon">
					<input type="password"  name="password" class="password-field" required>
					<span class="password-toggle">
						<i class="fa-solid fa-eye"></i>
					</span>
				</div>
			</div>
			<div class="password-wrapper" style="position: relative; max-width: 400px;">
				<label for="password"><strong>Confirm password</strong></label>
				<div class="input-with-icon">
					<input type="password"  name="password" class="password-field" required>
					<span class="password-toggle">
						<i class="fa-solid fa-eye"></i>
					</span>
				</div>
			</div>
			<button type="submit" class="btn" name="login">Create account</button>
			<div class="login-links">
				<a href="/barbershopSupplies/public/login.php">Already have an account? Log in</a><br>
			</div>
		</form>
		</main>
		<?php 
        include '../includes/footer.php'
        ?>
		<script>
			setTimeout(function () {
				const alert = document.querySelector('.alert');
				if (alert) {
				alert.style.transition = 'opacity 0.5s ease';
				alert.style.opacity = '0';
				setTimeout(() => alert.remove(), 500); 
				}
			}, 3000); 

			//password eye toggle
			document.addEventListener('DOMContentLoaded', () => {
			const toggles = document.querySelectorAll('.password-toggle');
			toggles.forEach(toggle => {
				const input = toggle.previousElementSibling;   
				const icon = toggle.querySelector('i');

				toggle.addEventListener('click', () => {
					const type = input.type === 'password' ? 'text' : 'password';
					input.type = type;

					icon.classList.toggle('fa-eye');
					icon.classList.toggle('fa-eye-slash');
				});
			});
		});
		</script>
	</body>
</html>