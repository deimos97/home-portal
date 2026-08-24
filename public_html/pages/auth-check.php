<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'hotwheelsandbids.com',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (empty($_SESSION['authenticated'])) {
    http_response_code(401);
    exit;
}
http_response_code(200);