<?php

namespace Colada\iter\pcntl;

/**
 * @psalm-template K
 * @psalm-template V
 *
 * @psalm-param iterable<K,V>
 *
 * @psalm-return iterable<K,V>
 */
function with_signal_break(iterable $inner): iterable
{
    $stop = false;
    $handler = function () use (&$stop) {
        $stop = true;
    };
    pcntl_signal(SIGINT, $handler);
    pcntl_signal(SIGTERM, $handler);

    foreach ($inner as $k => $v) {
        yield $k => $v;

        if ($stop) {
            break;
        }
    }
}
