<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class CustomHeap
    extends \SplHeap
{
    /**
     * @var callable
     */
    private $comparator;

    public function __construct($comparator)
    {
        Contracts::ensureCallable($comparator);

        $this->comparator = $comparator;
    }

    public function compare($element1, $element2)
    {
        // Some better syntax?
        $comparator = $this->comparator;

        // For some reasons, elements in heap are in descending order...
        // TODO Result type check?
        return -call_user_func($comparator, $element1, $element2);
    }
}
