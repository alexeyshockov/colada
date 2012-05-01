<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class CollectionMapIterator implements \Iterator
{
    protected $iterator;

    private $mapper;

    private $key;

    private $element;

    private $index = 0;

    public function __construct(\Iterator $iterator, callable $mapper)
    {
        $this->iterator = $iterator;
        $this->mapper   = $mapper;

        $this->prepareCurrent();
    }

    // TODO Duplicate.
    protected function clear()
    {
        $this->index   = 0;
        $this->key     = null;
        $this->element = null;
    }

    protected function prepareCurrent()
    {
        // Some better syntax?
        $mapper = $this->mapper;

        if ($this->iterator->valid()) {
            $this->element = $mapper($this->iterator->current());
            $this->key     = $this->index;

            return new Some($this->element);
        }

        return new None();
    }

    public function next()
    {
        $this->index++;

        $this->iterator->next();

        $this->prepareCurrent()->orElse(array($this, 'clear'));
    }

    public function key()
    {
        if (!$this->valid()) {
            // Or return NULL like ArrayIterator?
            throw new \RuntimeException();
        }

        return $this->key;
    }

    public function current()
    {
        if (!$this->valid()) {
            // Like \SplFixedArray. Or return NULL like ArrayIterator?
            throw new \RuntimeException();
        }

        return $this->element;
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();

        $this->clear();

        $this->prepareCurrent();
    }
}
