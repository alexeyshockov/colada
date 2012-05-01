<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class CustomHeap
    extends \SplHeap
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
        // Some better syntax?
        $comparator = $this->comparator;

        // For some reasons, elements in heap are in descending order...
        // TODO Result type check?
        return -$comparator($element1, $element2);
    }
}
