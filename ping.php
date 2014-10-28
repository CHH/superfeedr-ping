<?php

function verify_payload($payloadData)
{
    $signature = 'sha1='.hash_hmac('sha1', $payloadData, @$_SERVER['HOOK_SECRET']);

    return $signature === @$_SERVER['HTTP_X_HUB_SIGNATURE'];
}

$feedUrl = @$_SERVER['FEED_URL'] ?: 'http://christophh.net/atom.xml';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    return;
}

$payload = file_get_contents('php://input');

if (verify_payload($payload) === false) {
    fwrite('php://stderr', "Payload was not valid: ${payload}\n");
    header('HTTP/1.1 400 Bad Request');
    return;
}

$context = stream_context_create(['http' => [
    'method' => 'POST',
    'content' => http_build_query([
        'hub.mode' => 'publish',
        'hub.url' => 'http://www.christophh.net/atom.xml'
    ]),
]]);

$response = file_get_contents('http://christophh.superfeedr.com', false, $context);
fwrite('php://stderr', "Superfeedr: ".$response."\n");

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
]);
