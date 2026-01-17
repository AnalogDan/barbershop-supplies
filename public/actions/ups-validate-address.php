<?php
require_once __DIR__ . '/../../config.php';
require BASE_PATH . 'includes/ups-client.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON');
    }
    $token = upsGetAccessToken();
    $payload = [
        "XAVRequest" => [
            "AddressKeyFormat" => [
                "AddressLine" => [$input['street']],
                "PoliticalDivision2" => $input['city'],
                "PoliticalDivision1" => $input['state'],
                "PostcodePrimaryLow" => $input['zip'],
                "CountryCode" => "US"
            ]
        ]
    ];
    $ch = curl_init('https://onlinetools.ups.com/api/addressvalidation/v1/validate');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception(curl_error($ch));
    }
    curl_close($ch);
    $data = json_decode($response, true);

    // UPS logic: Valid if no "NoCandidatesIndicator"
    $isValid = !isset(
        $data['XAVResponse']['NoCandidatesIndicator']
    );

    echo json_encode([
        'success' => $isValid,
        'ups_response' => $data
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}