<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
session_start();

$message = "";

$token = $_GET['token'] ?? '';

if (!$token) {
    $message = "Invalid activation link.";
} else {
    // Find user with this token
    $stmt = $pdo->prepare("SELECT id, is_active FROM users WHERE activation_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Invalid or expired token.";
    } elseif ($user['is_active'] == 1) {
        $message = "Your account is already activated. You can log in.";
    } else {
        // Activate the account
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);

        $message = "Your account has been activated! You can now log in.";
    }
}

$currentPage = '';
?> 
<!DOCTYPE html>
<style>
.activation-message {
    padding: 1.2rem;
    margin: 6rem auto;
    max-width: 600px;
    font-size: 1.3rem;
    font-weight: 600;
    text-align: center;
}
</style>
<html lang="en">
	<?php include BASE_PATH . 'includes/head.php'; ?>
	<body>
		<main>
            <?php if (!empty($message)): ?>
                <div class="activation-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
		</main>
		<?php 
        ?>
		<script>
		</script>
	</body>
</html>