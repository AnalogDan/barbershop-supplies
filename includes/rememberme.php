<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

if (
    !isset($_SESSION['user_id']) &&
    isset($_COOKIE['rememberme']) &&
    !empty($_COOKIE['rememberme'])
) {
    $cookieToken = $_COOKIE['rememberme'];
    $hashedToken = hash('sha256', $cookieToken);

    $stmt = $pdo->prepare("SELECT id, email, first_name FROM users WHERE remember_token = ?");
    $stmt->execute([$hashedToken]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Restore login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_first_name'] = $user['first_name']; 
    }
}

//Restore cart
$tz = new DateTimeZone('America/Los_Angeles');
$now = new DateTime('now', $tz);
$createdAt = $now->format('Y-m-d H:i:s');
if (!isset($_SESSION['cart_id'])) {

    // Case 1: Logged-in user
    if (isset($_SESSION['user_id'])) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM carts
            WHERE user_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cartId = $stmt->fetchColumn();

        if ($cartId) {
            $_SESSION['cart_id'] = (int) $cartId;
        } else {
            // Create new cart for user
            $stmt = $pdo->prepare("
                INSERT INTO carts (user_id, created_at)
                VALUES (?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $createdAt]);

            $_SESSION['cart_id'] = (int) $pdo->lastInsertId();
        }

    } else {
        // Case 2: Guest user

        $stmt = $pdo->prepare("
            INSERT INTO carts (created_at)
            VALUES (?)
        ");
        $stmt->execute([$createdAt]);
        $_SESSION['cart_id'] = (int) $pdo->lastInsertId();
    }
}
?>