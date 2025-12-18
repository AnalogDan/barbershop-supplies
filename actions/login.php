<?php
require_once __DIR__ . '/../includes/db.php';
session_start();
header('Content-Type: application/json');

//Validate data
if (empty($_POST['email']) || empty($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required.'
    ]);
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

//Email exists? Password good? Is active? In that order
$stmt = $pdo->prepare("SELECT id, email, password_hash, is_active, first_name FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Account not found.'
    ]);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Incorrect password.'
    ]);
    exit;
}

if ((int)$user['is_active'] !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Please activate your account before logging in.'
    ]);
    exit;
}


//Remember session token (30 days open)
$token = bin2hex(random_bytes(32)); 
setcookie(
    "rememberme",
    $token,
    time() + (86400 * 30), 
    "/",
    "",
    true,
    true
);
$hashedToken = hash('sha256', $token);
$stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
$stmt->execute([$hashedToken, $user['id']]);

//Store session data
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_first_name'] = $user['first_name'];
$userId = (int)$user['id'];

//CART SHIT - fetch guest and user carts
$guestCartId = $_SESSION['cart_id'] ?? null;
$stmt = $pdo->prepare("
    SELECT id
    FROM carts
    WHERE user_id = ?
    LIMIT 1
");
$stmt->execute([$userId]);
$userCartId = $stmt->fetchColumn();

//If user has no cart, but guest does, transfer it to user
$guestCartPromoted = false;
if ($guestCartId && !$userCartId) {

    $stmt = $pdo->prepare("
        UPDATE carts
        SET user_id = ?
        WHERE id = ?
    ");
    $stmt->execute([$userId, $guestCartId]);
    $userCartId = $guestCartId;
    $guestCartPromoted = true;
}
//Both user and guest have cars, merge 'em (it works because the if $guestItems is empty the loop doesn't execute, duh)
if ($guestCartId && $userCartId && !$guestCartPromoted) {
    $stmt = $pdo->prepare("
        SELECT product_id, quantity
        FROM cart_items
        WHERE cart_id = ?
    ");
    $stmt->execute([$guestCartId]);
    $guestItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($guestItems)){
        foreach ($guestItems as $item) {
            $productId = $item['product_id'];
            $guestQty  = $item['quantity'];

            // Fetch stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $stock = (int)$stmt->fetchColumn();

            // Fetch user quantity
            $stmt = $pdo->prepare("
                SELECT quantity
                FROM cart_items
                WHERE cart_id = ? AND product_id = ?
            ");
            $stmt->execute([$userCartId, $productId]);
            $userQty = $stmt->fetchColumn();

            //Cap quantity at stock value
            if ($userQty !== false) {
                $newQty = min($userQty + $guestQty, $stock);

                $stmt = $pdo->prepare("
                    UPDATE cart_items
                    SET quantity = ?
                    WHERE cart_id = ? AND product_id = ?
                ");
                $stmt->execute([$newQty, $userCartId, $productId]);
            } else {
                $insertQty = min($guestQty, $stock);

                $stmt = $pdo->prepare("
                    INSERT INTO cart_items (cart_id, product_id, quantity)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$userCartId, $productId, $insertQty]);
            }
        }
    }
}
//Delete guest cart 
if (!$guestCartPromoted && $guestCartId) {
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$guestCartId]);
    $stmt = $pdo->prepare("DELETE FROM carts WHERE id = ?");
    $stmt->execute([$guestCartId]);
}
//Save the user cart in session
$_SESSION['cart_id'] = $userCartId;


echo json_encode([
    'success' => true,
    'message' => 'Login successful!'
]);
exit;

?>