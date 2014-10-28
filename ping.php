<?php

function verify_payload($payloadData)
{
    $signature = 'sha1='.hash_hmac('sha1', $payloadData, @$_SERVER['HOOK_SECRET']);

    return $signature === @$_SERVER['HTTP_X_HUB_SIGNATURE'];
}

function heroku_log($string, $variables = [])
{
    static $log;

    if (null === $log) {
        $log = fopen('php://stderr', 'wb');
    }

    if ($variables) {
        $string .= ' '.join(' ', array_map(function($key, $value) {
            return "$key=$value";
        }, array_keys($variables), array_values($variables)));
    }

    fwrite($log, $string."\n");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    return;
}

$logs = fopen('php://stderr', 'wb');
$payload = file_get_contents('php://input');

if (verify_payload($payload) === false) {
    heroku_log("Payload was not valid: ${payload}\n");
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
heroku_log("Superfeedr: ".strlen($response)." Bytes", [
    'headers' => json_encode($http_response_header)
]);

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
]);
