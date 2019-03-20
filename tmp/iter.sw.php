<?php

namespace Colada\iter\sw;

use Iterator;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

function each_n_and_last(callable $action, int $n, iterable $flow, StopwatchEvent $stopwatchEvent = null): Iterator
{
    $processed = 0;
    $key = $value = null; // In case $flow is empty

    // Null coalescing assignment operator is only available in PHP 7.4+ :(
    $stopwatchEvent = $stopwatchEvent ?? (new Stopwatch())->start(__FUNCTION__ . ' ' . rand(0, PHP_INT_MAX));

    $stopwatchEvent->start();

    foreach ($flow as $key => $value) {
        yield $key => $value;

        $processed++;

        if ($processed % $n === 0) {
            $stopwatchEvent->stop();
            $period = array_pop($periods = $stopwatchEvent->getPeriods());

            try {
                $action($processed, $period);
            } finally {
                $stopwatchEvent->start();
            }
        }
    }

    if ($processed !== 0) {
        $stopwatchEvent->stop();
        // Getting the last period and add it as the first argument
        $periods = $stopwatchEvent->getPeriods();
        $period = array_pop($periods);

        $action($processed, $period, $value, $key);
    }
}
