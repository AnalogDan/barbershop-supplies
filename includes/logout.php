<?php
session_start();

$_SESSION = [];
session_destroy();

setcookie('rememberme', '', time() - 3600, '/', '', false, true);

header("Location: ../public/login.php");
exit;
?>