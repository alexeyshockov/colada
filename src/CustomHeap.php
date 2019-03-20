<?php

namespace Colada;

final class CustomHeap extends \SplHeap
{
    /**
     * @var callable
     */
    private $comparator;

    public function __construct(callable $comparator)
    {
        $this->comparator = $comparator;
    }

    public function compare($element1, $element2)
    {
        // For some reasons, elements in heap are in descending order...
        return -call_user_func($this->comparator, $element1, $element2);
    }
}
