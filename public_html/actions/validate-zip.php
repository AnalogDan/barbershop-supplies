<?php
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . 'includes/db.php';

header('Content-Type: application/json');

$zip = trim($_POST['zip'] ?? '');
$state = trim($_POST['state'] ?? '');

// Basic validation
if ($zip === '' || $state === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Missing ZIP code or state.'
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 1
    FROM zip_codes
    WHERE zip_code = ?
      AND state = ?
    LIMIT 1
");

$stmt->execute([$zip, $state]);

$isValid = (bool) $stmt->fetchColumn();

echo json_encode([
    'success' => $isValid,
    'message' => $isValid
        ? 'ZIP code validated.'
        : 'The ZIP code does not match the selected state.'
]);
