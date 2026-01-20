<?php
require_once __DIR__ . '/../config.php';
session_start();

$_SESSION = [];
session_destroy();

setcookie('rememberme', '', time() - 3600, '/', '', false, true);

header(header: "Location: " . BASE_URL . "login.php");
exit;
?>