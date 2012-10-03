<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class TailIterator extends \IteratorIterator
{
    public function __construct($iterator)
    {
        parent::__construct($iterator);
    }

    public function rewind()
    {
        parent::rewind();

        // Skip head.
        $this->next();
    }
}
