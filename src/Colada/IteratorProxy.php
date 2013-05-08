<?php

namespace Colada;

/**
 * @internal
 *
 * For PHP's foreach call sequence are: rewind() at start and then valid(), key(), current() and next() for a while.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IteratorProxy implements \Iterator
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    // In foreach and all other function rewind() will be called first.
    public function rewind()
    {
        $this->iterator->rewind();
    }
}
