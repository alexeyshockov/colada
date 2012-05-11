<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IteratorProxy implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

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

    public function rewind()
    {
        $this->iterator->rewind();
    }
}
