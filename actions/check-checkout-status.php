<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    echo json_encode(['status' => 'error']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT status
    FROM checkout_sessions
    WHERE stripe_session_id = ?
    LIMIT 1
");
$stmt->execute([$sessionId]);

$status = $stmt->fetchColumn();

echo json_encode([
    'status' => $status ?: 'pending'
]);