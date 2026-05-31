<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';
session_start();
if (isset($_SESSION['admin_id'])) {
    $stmt = $pdo->prepare("
        UPDATE admins
        SET remember_token_hash = NULL,
        remember_token_expires = NULL
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['admin_id']]);
}
setcookie(
    'admin_remember',
    '',
    time() - 3600,
    '/',
    '',
    true,
    true
);
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
