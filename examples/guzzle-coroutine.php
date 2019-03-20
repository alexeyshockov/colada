<?php

use GuzzleHttp\Client;
use function Colada\GuzzleHttp\coroutine_invoke;

require '../vendor/autoload.php';

$client = new Client();

$coroutine = coroutine_invoke(function () use ($client) {
    $names = [
        'ya.ru',
        'yandex.ru',
        'google.com',
        'goo.gl',
    ];

    $responses = yield array_map([$client, 'getAsync'], $names);

    foreach ($responses as $response) {
        echo "Code: {$response->getStatusCode()}\n";
    };

    return 'Success!';
});

$result = $coroutine->wait();

echo "$result\n";
