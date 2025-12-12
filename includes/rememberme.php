<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php'; // Make sure $pdo is available

// If user is not logged in but cookie exists → try auto-login
if (
    !isset($_SESSION['user_id']) &&
    isset($_COOKIE['rememberme']) &&
    !empty($_COOKIE['rememberme'])
) {
    $cookieToken = $_COOKIE['rememberme'];
    $hashedToken = hash('sha256', $cookieToken);

    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE remember_token = ?");
    $stmt->execute([$hashedToken]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Restore login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_first_name'] = $row['first_name']; 
    }
}
?>