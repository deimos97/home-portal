<?php
// Órdenes al coche RC (ESP32 en 192.168.0.2). Solo con sesión: lo garantiza el router.
$layout = 'none';
header('Content-Type: application/json');
header('Cache-Control: no-store');

function curlRequest(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,   // short timeout — a joystick command should fail fast, not hang
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['http_code' => $httpCode, 'body' => $response];
}

$valid_commands = ["go", "back", "left", "right", "stop"];
$command = $_GET['cmd'] ?? '';

if (!in_array($command, $valid_commands, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid command']);
    exit;
}

$result = curlRequest("http://192.168.0.2/{$command}");
if ($result['http_code'] !== 200) {
    http_response_code(502);
}
echo json_encode($result);
