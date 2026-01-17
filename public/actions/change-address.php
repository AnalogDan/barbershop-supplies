<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit;
}

$required = ['full_name', 'street', 'city', 'zip', 'state'];

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill all required fields.'
        ]);
        exit;
    }
}

$userId = $_SESSION['user_id'];

//Select promary address id
$stmt = $pdo->prepare("
    SELECT id
    FROM user_addresses
    WHERE user_id = ? AND is_primary = 1
    LIMIT 1
");
$stmt->execute([$userId]);
$primary = $stmt->fetch(PDO::FETCH_ASSOC);

//If there's a primary one, update, else, add it 
if ($primary) {
    $stmt = $pdo->prepare("
        UPDATE user_addresses
        SET full_name = ?, street = ?, city = ?, zip = ?, state = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['full_name'],
        $_POST['street'],
        $_POST['city'],
        $_POST['zip'],
        $_POST['state'],
        $primary['id']
    ]);
} else {
    //Make all addresses not primary for safety, newest is primary
    $pdo->prepare("
        UPDATE user_addresses
        SET is_primary = 0
        WHERE user_id = ?
    ")->execute([$userId]);

    $stmt = $pdo->prepare("
        INSERT INTO user_addresses
            (user_id, full_name, street, city, zip, state, is_primary)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");

    $stmt->execute([
        $userId,
        $_POST['full_name'],
        $_POST['street'],
        $_POST['city'],
        $_POST['zip'],
        $_POST['state']
    ]);
}

echo json_encode(['success' => true]);