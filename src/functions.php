<?php

namespace Colada;

use Carbon\CarbonInterval;
use DateInterval;
use DateTime;
use Exception;
use RuntimeException;

/**
 * @param float|int $size
 * @param int $precision
 *
 * @return string
 */
function format_bytes($size, int $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = ['', 'K', 'M', 'G', 'T'];

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

/**
 * @param float $ms
 *
 * @return DateInterval|CarbonInterval
 */
function ms2interval(float $ms): DateInterval
{
    $ms /= 1000;
    $timeLeft = floor($ms);
    $ms = round(($ms - $timeLeft) * 1000);

    // Don't work with values more than 1000 milliseconds :(
//    $t2 = $t1->modify("+$timeLeft milliseconds");

    // CarbonInterval::milliseconds() also works like the code above, doesn't handle transformations

    try {
        $interval = (new DateTime('@0'))->diff(
            (new DateTime("@$timeLeft"))->modify("+$ms milliseconds")
        );
    } catch (Exception $e) {
        throw new RuntimeException('Date format is invalid');
    }

    return class_exists(CarbonInterval::class) ? CarbonInterval::instance($interval) : $interval;
}
