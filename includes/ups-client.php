<?php
function upsGetAccessToken(): string
{
    $config = require __DIR__ . '/../config/ups.php';
    $ch = curl_init($config['oauth_url']);
    $postData = http_build_query([
        'grant_type' => 'client_credentials'
    ]);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded'
        ],
        CURLOPT_USERPWD => $config['client_id'] . ':' . $config['client_secret']
    ]);
    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('UPS OAuth error: ' . curl_error($ch));
    }
    curl_close($ch);
    $data = json_decode($response, true);
    if (!isset($data['access_token'])) {
        throw new Exception('UPS OAuth failed: ' . $response);
    }

    return $data['access_token'];
}