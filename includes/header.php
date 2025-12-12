<?php
session_start();
	require_once __DIR__ . '/rememberme.php';
	$error = $_SESSION['login_error'] ?? null;
	unset($_SESSION['login_error']);
	$currentPage = 'account';

	//echo password_hash("barberThings", PASSWORD_DEFAULT);
	// username: barberAdmin
?>