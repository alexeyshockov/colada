<?php

namespace Colada;

/**
 * Builder for constructing immutable collections.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class CollectionBuilder
{
    /**
     * @var \SplFixedArray
     */
    protected $array;

    private $index = 0;

    /**
     * Useful helper for PHP versions less than 5.4.
     *
     * @param mixed $collection
     *
     * @return Collection
     */
    public static function buildFrom($collection)
    {
        $builder = new static($collection);

        return $builder->addAll($collection)->build();
    }

    /**
     * @param int|\Countable|mixed $sizeHint Hint for resulting collection size.
     */
    public function __construct($sizeHint = 0)
    {
        // "Like other" collection.
        if (is_object($sizeHint) && ($sizeHint instanceof \Countable)) {
            $sizeHint = count($sizeHint);
        }

        if (!is_numeric($sizeHint)) {
            $sizeHint = 1;
        }

        $this->array = new \SplFixedArray($sizeHint);
    }

    /**
     * @param mixed $element
     *
     * @return CollectionBuilder
     */
    public function add($element)
    {
        // Index can not be greater then array size.
        if (count($this->array) == $this->index) {
            $this->array->setSize($this->index + 1);
        }

        $this->array[$this->index++] = $element;

        return $this;
    }

    /**
     * @param mixed $elements
     *
     * @return CollectionBuilder
     */
    public function addAll($elements)
    {
        if (!(is_array($elements) || (is_object($elements) && ($elements instanceof \Traversable)))) {
            // Process not iterable values gracefully.
            $elements = array($elements);
        }

        if (is_array($elements) || (is_object($elements) && ($elements instanceof \Countable))) {
            if (count($this->array) < ($this->index + count($elements))) {
                $this->array->setSize($this->index + count($elements));
            }

            foreach ($elements as $element) {
                $this->array[$this->index++] = $element;
            }
        } else {
            foreach ($elements as $element) {
                $this->add($element);
            }
        }

        return $this;
    }

    /**
     * @return CollectionBuilder
     */
    public function clear()
    {
        // Restore previous size?
        $this->array->setSize(0);

        return $this;
    }

    /**
     * @return Collection
     */
    public function build()
    {
        // Shrink array to actual size.
        $this->array->setSize($this->index);

        return $this->createCollection();
    }

    protected function createCollection()
    {
        return new IteratorCollection($this->array);
    }

    public function __clone()
    {
        $this->array = clone $this->array;
    }
}
