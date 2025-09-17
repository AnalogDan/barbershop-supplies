<?php
	require_once __DIR__ . '/../includes/db.php';
	session_start();
	$error = $_SESSION['login_error'] ?? null;
	unset($_SESSION['login_error']);

	//echo password_hash("barberThings", PASSWORD_DEFAULT);
	// username: barberAdmin
?> 
<!DOCTYPE html>
<style>
	.login-links {
		text-align: right; 
	}
	.login-links a { 
		font-weight: 600;  
		color: #666;          
	}
	.login-links a:hover {
		color: #333;           
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
				<h2>Login</h2>
				<img src="/barbershopSupplies/public/images/Ornament1.png" alt="Ornament">
		</div>
		<form class="admin-login-form" action="/barbershopSupplies/includes/process-login.php" method="POST">
			<div>
				<label for="email"><strong>Email</strong></label>
				<input id="email" type="text" name="email" required>
			</div>
			<div class="password-wrapper" style="position: relative; max-width: 400px;">
				<label for="password"><strong>Password</strong></label>
				<div class="input-with-icon">
					<input id="password" type="password"  name="password" required>
					<span id="togglePassword" class="password-toggle">
						<i class="fa-solid fa-eye"></i>
					</span>
				</div>
			</div>
			<div class="login-links">
				<a href="/barbershopSupplies/public/forgot-password.php">Forgot password?</a><br>
				<a href="/barbershopSupplies/public/register.php">Create account</a>
			</div>
			<button type="submit" class="btn" name="login">Sign in</button>
		</form>
		</main>
		<?php 
        include '../includes/footer.php'
        ?>
		<script src="/barbershopSupplies/public/js/password-toggle.js"></script>
		<script>
			setTimeout(function () {
				const alert = document.querySelector('.alert');
				if (alert) {
				alert.style.transition = 'opacity 0.5s ease';
				alert.style.opacity = '0';
				setTimeout(() => alert.remove(), 500); 
				}
			}, 3000); 
		</script>
	</body>
</html>