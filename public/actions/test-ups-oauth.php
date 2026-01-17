<?php
require_once __DIR__ . '/../../config.php';
require BASE_PATH . 'includes/ups-client.php';

try {
    $token = upsGetAccessToken();
    echo 'UPS OAuth OK<br>';
    echo substr($token, 0, 30) . '...';
} catch (Exception $e) {
    echo $e->getMessage();
}