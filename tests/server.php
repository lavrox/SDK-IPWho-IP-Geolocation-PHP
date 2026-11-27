<?php
// Router for PHP's built-in web server: a stand-in for the API.
// It records each request and sends back whatever the test asked for.
$dir = getenv('IPWHO_STUB_DIR');
file_put_contents($dir . '/requests.log', json_encode([
    'uri'           => $_SERVER['REQUEST_URI'],
    'authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
    'userAgent'     => $_SERVER['HTTP_USER_AGENT'] ?? null,
]) . "\n", FILE_APPEND);
$reply = json_decode(file_get_contents($dir . '/reply.json'), true);
http_response_code($reply['status']);
header('Content-Type: application/json');
echo $reply['body'];
