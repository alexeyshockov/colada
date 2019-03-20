<?php

namespace Colada\iter;

use Colada\CustomHeap;
use Iterator;

/**
 * @api
 *
 * @param iterable $flow
 *
 * @return Iterator
 */
function to_kv_pairs(iterable $flow): Iterator
{
    foreach ($flow as $key => $value) {
        yield [$key, $value];
    }
}

/**
 * @api
 *
 * @param callable $action
 * @param int      $n
 * @param iterable $flow
 *
 * @return Iterator
 */
function each_n_and_last(callable $action, int $n, iterable $flow): Iterator
{
    $processed = 0;
    $key = $value = null; // In case $flow is empty

    foreach ($flow as $key => $value) {
        yield $key => $value;

        $processed++;

        if ($processed % $n === 0) {
            $action($processed);
        }
    }

    if ($processed % $n !== 0) {
        $action($processed);
    }
}

/**
 * @api
 *
 * @param callable $comparator
 * @param iterable $flow
 *
 * @return Iterator
 */
function uasort(callable $comparator, iterable $flow): Iterator
{
    $tupleValueComparator = function ($tuple1, $tuple2) use ($comparator) {
        return $comparator($tuple1[1], $tuple2[1]);
    };

    $heap = new CustomHeap($tupleValueComparator);
    foreach ($flow as $key => $value) {
        $heap->insert([$key, $value]);
    }

    foreach ($heap as list($key, $value)) {
        yield $key => $value;
    }
}

/**
 * @api
 *
 * @param callable $comparator
 * @param iterable $flow
 *
 * @return Iterator
 */
function uksort(callable $comparator, iterable $flow): Iterator
{
    $tupleKeyComparator = function ($tuple1, $tuple2) use ($comparator) {
        return $comparator($tuple1[0], $tuple2[0]);
    };

    $heap = new CustomHeap($tupleKeyComparator);
    foreach ($flow as $key => $value) {
        $heap->insert([$key, $value]);
    }

    foreach ($heap as list($key, $value)) {
        yield $key => $value;
    }
}
