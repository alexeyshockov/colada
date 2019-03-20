<?php

use React\Dns\Config\Config;
use React\EventLoop\Factory;
use function Colada\React\coroutine_invoke;

require '../vendor/autoload.php';

$loop = Factory::create();

$config = Config::loadSystemConfigBlocking();
$server = $config->nameservers ? reset($config->nameservers) : '8.8.8.8';

$factory = new React\Dns\Resolver\Factory();
$dns = $factory->create($server, $loop);

$coroutine = coroutine_invoke(function () use ($dns) {
    $names = [
        'ya.ru',
        'yandex.ru',
        'google.com',
        'goo.gl',
    ];

    $ips = yield array_map([$dns, 'resolve'], $names);

    foreach ($ips as $ip) {
        echo "Host: $ip\n";
    };

    return 'Success!';
});

$loop->run();
