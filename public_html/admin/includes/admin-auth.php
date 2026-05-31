<?php

session_start();

if (!empty($_SESSION['admin_logged_in'])) {
    return;
}

if (empty($_COOKIE['admin_remember'])) {
    return;
}

$token = $_COOKIE['admin_remember'];

$stmt = $pdo->query("
    SELECT *
    FROM admins
    WHERE remember_token_hash IS NOT NULL
      AND remember_token_expires > NOW()
");

$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($admins as $admin) {

    if (password_verify($token, $admin['remember_token_hash'])) {

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        return;
    }
}
