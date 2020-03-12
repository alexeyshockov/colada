<?php

use Colada\ProcessingStopwatch;
use Colada\ProcessingStopwatchLap;

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
$total = 7;

$flow = ProcessingStopwatch::wrap(
    $g(), 2, function (ProcessingStopwatchLap $lap) use ($total) {
        echo 'Time left ~ ' . $lap->overall()->eta($total)->format('%H:%I:%S') . PHP_EOL;
    }
);

foreach ($flow as $item) {} // Go over the generator
