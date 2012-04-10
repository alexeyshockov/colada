<?php

namespace Cormorant;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class CollectionFlatMapIterator extends CollectionMapIterator
{
    private $flattingIterator;

    private $mapper;

    public function __construct(\Iterator $iterator, callable $mapper)
    {
        $this->flattingIterator = $iterator;
        $this->mapper           = $mapper;

        parent::__construct(
            $this->prepareCurrentIterator()->orElse(new \ArrayIterator(array())),
            function($element) { return $element; }
        );
    }

    protected function prepareCurrent()
    {
        while ($this->flattingIterator->valid() && (parent::prepareCurrent() instanceof None)) {
            $this->flattingIterator->next();

            $this->iterator = $this->prepareCurrentIterator()->orElse(new \ArrayIterator(array()));
        }

        return parent::prepareCurrent();
    }

    private function prepareCurrentIterator()
    {
        // Some better syntax?
        $mapper = $this->mapper;

        if ($this->flattingIterator->valid()) {
            $elements = $mapper($this->flattingIterator->current());

            if (!(is_array($elements) || (is_object($elements) && ($elements instanceof \Traversable)))) {
                // Process not iterable values gracefully.
                $elements = array($elements);
            }

            if (is_array($elements)) {
                $elements = new \ArrayIterator($elements);
            } else {
                // \Traversable != \Iterator
                if (!($elements instanceof \Iterator)) {
                    $elements = new \IteratorIterator($elements);
                }
            }

            return new Some($elements);
        }

        return new None();
    }

    public function rewind()
    {
        $this->flattingIterator->rewind();

        $this->iterator = $this->prepareCurrentIterator()->orElse(new \ArrayIterator(array()));

        parent::rewind();
    }
}
