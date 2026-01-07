<?php
	require_once __DIR__ . '/../includes/db.php';
	require_once __DIR__ . '/../includes/header.php';
	if (isset($_SESSION['user_id'])) {
    	header("Location: /barbershopSupplies/public/my-profile.php");
    	exit;
	}
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
		<form class="admin-login-form" id="login-form" novalidate>
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
				<a href="/barbershopSupplies/public/register.php">Create account</a><br>

				<!-- DELETE LATER, alt my profile entry point -->
				<a href="/barbershopSupplies/public/my-profile.php">Alt My Profile</a>
			</div>
			<button type="submit" class="btn" name="login">Sign in</button>
		</form>
		</main>
		<?php 
        include '../includes/footer.php';
		include '../includes/modals.php';
        ?>
		<script src="/barbershopSupplies/public/js/password-toggle.js"></script>
		<script>
			// showAlertModal("Test alert.", () => {});
			// showConfirmModal(
			// 	`Test confirm`,
			// 	() => {
			// 	},
			// 	() => {
			// 	}
			// );
			setTimeout(function () {
				const alert = document.querySelector('.alert');
				if (alert) {
				alert.style.transition = 'opacity 0.5s ease';
				alert.style.opacity = '0';
				setTimeout(() => alert.remove(), 500); 
				}
			}, 3000); 

			//Submit form
			document.getElementById('login-form').addEventListener('submit', async (e) => {
				e.preventDefault();
				if (!validateForm()) return;
				const form = document.getElementById('login-form');
				const formData = new FormData(form);
				const response = await fetch('../actions/login.php', {
					method: 'POST',
					body: formData
				});
				const result = await response.json();
				if (result.success) {
					// showAlertModal(result.message, () => {
					// 	window.location.href = "home.php";
					// });
					window.location.href = "home.php";
				} else {
					showAlertModal(result.message);
				}
			})

			//Validate form
			function validateForm(){
				const email = document.getElementById('email').value.trim();
				const password = document.getElementById('password').value;
				if (email === '' || password === '') {
					showAlertModal("Please fill all the fields.");
					return false;
				}
				if (!email.includes('@')) {
					showAlertModal('Invalid email format.');
					return false;
				}
				return true;
			}

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