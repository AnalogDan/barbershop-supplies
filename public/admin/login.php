<?php
	require_once __DIR__ . '/../includes/db.php';
	session_start();
	$error = $_SESSION['login_error'] ?? null;
	unset($_SESSION['login_error']);
?>
<?php
	//echo password_hash("barberThings", PASSWORD_DEFAULT);
	// username: barberAdmin
?> 
<!DOCTYPE html>
<html lang="en">
	<?php include 'includes/admin_head.php'; ?>
	<body>
		<?php if ($error): ?>
			<div class="alert-container">
				<div class="alert alert-danger text-center rounded" role="alert">
					<?= htmlspecialchars(($error)) ?>
				</div>
			</div>
		<?php endif; ?>
		<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">
			<div class="container">
				<a class="navbar-brand" >New Vision Admin Panel</a>
			</div>
		</nav>
		<main>
		<div class="section-title">
				<h2>Admin Login</h2>
				<img src="/barbershopSupplies/public/images/Ornament1.png" alt="Ornament">
		</div>
		<form class="admin-login-form" action="/barbershopSupplies/admin/process-login.php" method="POST">
			<div>
				<label for="email"><strong>User</strong></label>
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
			<button type="submit" class="btn" name="login">Sign in</button>
		</form>
		</main>
		<footer class="footer-section">
			<div class="container relative">
				<div class="mb-4 footer-logo-wrap"><span class="footer-logo">New Vision<br>Barber Supplies</span></div>
			</div>
			<div class="footer-ornament"></div>
		</footer>
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