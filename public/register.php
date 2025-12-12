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

	.loading {
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.4);
		backdrop-filter: blur(2px);
		display: none;
		align-items: center;
		justify-content: center;
		z-index: 9999;
		color: white;
		font-size: 1.5rem;
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
		<form class="admin-login-form" id="register-form" novalidate>
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
					<input type="password"  name="password_confirm" class="password-field" required>
					<span class="password-toggle">
						<i class="fa-solid fa-eye"></i>
					</span>
				</div>
			</div>
			<button type="submit" class="btn" name="login">Create account</button>
			<div class="loading" id="loading-overlay">
				Loading...
			</div>
			<div class="login-links">
				<a href="/barbershopSupplies/public/login.php">Already have an account? Log in</a><br>
			</div>
		</form>
		</main>
		<?php 
        include '../includes/footer.php';
		include '../includes/modals.php'; 
        ?>
		<script>
			showAlertModal("Test alert.", () => {});
			showConfirmModal(
				`Test confirm`,
				() => {
				},
				() => {
				}
			);

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

			//Loading functions
			function showLoading() {
				document.getElementById("loading-overlay").style.display = "flex";
			}
			function hideLoading() {
				document.getElementById("loading-overlay").style.display = "none";
			}

			//Submit form
			document.getElementById('register-form').addEventListener('submit', async (e) => {
				e.preventDefault();
				if (!validateForm()) return;
				showLoading();
				try{
					const form = document.getElementById('register-form');
					const formData = new FormData(form);
					const response = await fetch('../actions/register.php', {
						method: 'POST',
						body: formData
					});
					const result = await response.json();
					hideLoading();
					if (result.success) {
						showAlertModal(result.message, () => {
							window.location.href = "login.php";
						});
					} else {
						showAlertModal(result.message);
					}
				}catch(error){
					hideLoading();
					showAlertModal("Something went wrong. Please try again.");
				}
			})

			//Validate form (front end)
			function validateForm(){
				const firstName = document.getElementById('first-name').value.trim();
				const lastName  = document.getElementById('last-name').value.trim();
				const email     = document.getElementById('email').value.trim();
				const passwordFields = document.querySelectorAll('.password-field');
				const pass1 = passwordFields[0].value;
    			const pass2 = passwordFields[1].value;
				if (firstName === '') {
					showAlertModal("First name is required.");
					return false;
				}
				if (lastName === '') {
					showAlertModal("Last name is required.");
					return false;
				}
				if (!email.includes('@')) {
					showAlertModal("Please enter a valid email address.");
					return false;
				}
				if (pass1.length < 8) {
					showAlertModal("Password must be at least 8 characters long.");
					return false;
				}
				if (pass1 !== pass2) {
					showAlertModal("Passwords do not match.");
					return false;
				}
				console.log("All good. Ready to send fetch()");
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