<?php

function verify_payload($payloadData)
{
    if (empty($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
        return false;
    }

    $signature = 'sha1='.hash_hmac('sha1', $payloadData, $_SERVER['HOOK_SECRET']);

    return $signature === $_SERVER['HTTP_X_HUB_SIGNATURE'];
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

function ping_hub()
{
    $context = stream_context_create(['http' => [
        'method' => 'POST',
        'content' => http_build_query([
            'hub.mode' => 'publish',
            'hub.url' => $_SERVER['FEED_URL'],
        ]),
    ]]);

    $response = file_get_contents($_SERVER['HUB_URL'], false, $context);

    heroku_log("Superfeedr: ".strlen($response)." Bytes", [
        'headers' => json_encode($http_response_header)
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    return;
}

$payload = file_get_contents('php://input');

if (verify_payload($payload) === false) {
    heroku_log("Payload was not valid: ${payload}\n");
    header('HTTP/1.1 400 Bad Request');
    return;
}

ping_hub();

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
]);
