<?php
function getActiveCartId(PDO $pdo): int
{
    // Logged-in user 
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("
            SELECT id
            FROM carts
            WHERE user_id = ?
              AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cartId = $stmt->fetchColumn();

        if ($cartId) {
            $_SESSION['cart_id'] = (int)$cartId;
            return (int)$cartId;
        }

        // No active cart, create one
        $stmt = $pdo->prepare("
            INSERT INTO carts (user_id, status, created_at)
            VALUES (?, 'active', NOW())
        ");
        $stmt->execute([$_SESSION['user_id']]);

        $cartId = (int)$pdo->lastInsertId();
        $_SESSION['cart_id'] = $cartId;

        return $cartId;
    }

    // Guest user
    if (!empty($_SESSION['cart_id'])) {
        $stmt = $pdo->prepare("
            SELECT id
            FROM carts
            WHERE id = ?
              AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['cart_id']]);

        if ($stmt->fetchColumn()) {
            return (int)$_SESSION['cart_id'];
        }

        // Session cart exists but is completed
        unset($_SESSION['cart_id']);
    }

    // Create new guest cart
    $stmt = $pdo->prepare("
        INSERT INTO carts (user_id, status, created_at)
        VALUES (NULL, 'active', NOW())
    ");
    $stmt->execute();

    $cartId = (int)$pdo->lastInsertId();
    $_SESSION['cart_id'] = $cartId;

    return $cartId;
}