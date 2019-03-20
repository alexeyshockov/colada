<?php

use GuzzleHttp\Client;
use function Colada\GuzzleHttp\coroutine;
use function Colada\GuzzleHttp\time_sleep;
use function GuzzleHttp\Promise\all;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client();

$coroutine = coroutine(function ($num) use ($client) {
    $names = [
        'ya.ru',
        'yandex.ru',
        'google.com',
        'goo.gl',
    ];

    $responses = yield array_map([$client, 'getAsync'], $names);

    foreach ($responses as $response) {
        echo "[Coroutine $num] Code: {$response->getStatusCode()}\n";
    };

    $t = time();
    echo "[Coroutine $num] [time {$t}] Waiting for 3 second...\n";

    yield time_sleep(3);

    $t = time();
    return "[Coroutine $num] [time {$t}] Success!\n";
});

$threads = [
    $coroutine(1),
    $coroutine(2),
    $coroutine(3),
    $coroutine(4),
    $coroutine(5),
];

$result = all($threads)->wait();

var_dump($result);
