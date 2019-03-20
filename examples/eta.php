<?php

use Colada\ProgressBuilder;
use Colada\ProgressLap;

require '../vendor/autoload.php';

$g = function () {
    yield sleep(1);
    yield sleep(1);
    yield sleep(1);
    yield sleep(1);
    yield sleep(1);
    yield sleep(1);
    yield sleep(1);
};

$flow = ($progress = new ProgressBuilder())
    ->setTotal(7)
    ->setLapSize(1)
    ->setLapHandler(function (ProgressLap $lap) {
        echo 'Time left ~ ' . $lap->overall()->eta()->format('%H:%I:%S') . PHP_EOL;
    })
    ->startFor($g());

foreach ($flow as $item) {} // Go over the generator
