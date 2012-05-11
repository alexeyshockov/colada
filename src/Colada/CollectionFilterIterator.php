<?php

namespace Colada;

/**
 * @todo Extract common methods with CollectionMapIterator.
 *
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class CollectionFilterIterator implements \Iterator
{
    private $iterator;

    private $filter;

    private $key;

    private $element;

    private $index = 0;

    public function __construct(\Iterator $iterator, $filter)
    {
        Contracts::ensureCallable($filter);

        $this->iterator = $iterator;
        $this->filter   = $filter;

        $this->findCurrent();
    }

    private function clear()
    {
        $this->index   = 0;
        $this->key     = null;
        $this->element = null;
    }

    private function findCurrent()
    {
        while ($this->iterator->valid()) {
            if (call_user_func($this->filter, ($element = $this->iterator->current()))) {
                $this->element = $element;
                $this->key     = $this->index;

                return new Some($this->element);
            }

            $this->iterator->next();
        }

        return new None();
    }

    public function next()
    {
        $this->index++;

        $this->iterator->next();

        $this->findCurrent()->orElse(array($this, 'clear'));
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

        $this->findCurrent();
    }
}
