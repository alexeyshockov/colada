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
     * @var \ReflectionClass
     */
    private $class;

    /**
     * Useful helper for PHP versions less than 5.4.
     *
     * @param mixed $collection
     *
     * @return \Colada\Collection
     */
    public static function buildFrom($collection)
    {
        $builder = new static($collection);

        return $builder->addAll($collection)->build();
    }

    /**
     * @param \Countable|int|mixed $sizeHint Hint for resulting collection size.
     * @param string               $class    Collection class.
     */
    public function __construct($sizeHint = 0, $class = '\\Colada\\IteratorCollection')
    {
        // "Like other" collection.
        if (is_object($sizeHint) && ($sizeHint instanceof \Countable)) {
            $sizeHint = count($sizeHint);
        }

        if (!is_numeric($sizeHint)) {
            $sizeHint = 0;
        }

        $this->array = new \SplFixedArray($sizeHint);

        if (is_string($class)) {
            $class = new \ReflectionClass($class);
        }

        $this->class = $class;
    }

    /**
     * @param mixed $element
     *
     * @return \Colada\CollectionBuilder
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
     * @return \Colada\CollectionBuilder
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
     * @return \Colada\CollectionBuilder
     */
    public function clear()
    {
        // Restore previous size?
        $this->array->setSize(0);

        return $this;
    }

    /**
     * @return \Colada\Collection
     */
    public function build()
    {
        // Shrink array to actual size.
        $this->array->setSize($this->index);

        return $this->createCollection();
    }

    protected function createCollection()
    {
        return $this->class->newInstance($this->array);
    }

    public function __clone()
    {
        $this->array = clone $this->array;
    }
}
