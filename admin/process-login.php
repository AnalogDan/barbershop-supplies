<?php
    session_start();
    require_once __DIR__ . '/../includes/db.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $username = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if($username === '' || $password === ''){
            die('Please fill in both fields');
        }

        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_id'] = $user['id'];
                    header("Location: home.php");
                    exit;
                } else {
                    $_SESSION['login_error'] = "Invalid password.";
                }
            } else {
                $_SESSION['login_error'] = "Invalid username.";
            }
        } catch (PDOException $e) {
            $_SESSION['login_error'] = "Database error.";
        }
    } else {
        $_SESSION['login_error'] = "Request failed.";
    }

header("Location: login.php");
exit;
?>