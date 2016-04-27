<?php

namespace Colada;

use SplHeap;

/**
 * Container with internal ordering
 *
 * Useful for sorting.
 *
 * @internal
 */
class CustomHeap extends SplHeap
{
    /** @var callable */
    private $comparator;

    public function __construct(callable $comparator)
    {
        $this->comparator = $comparator;
    }

    public function compare($tuple1, $tuple2)
    {
        $result = call_user_func($this->comparator, $tuple1, $tuple2);

        // For some reasons, elements in heap are in descending order...
        return -$result;
    }
}
