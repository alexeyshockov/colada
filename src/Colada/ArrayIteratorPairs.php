<?php

namespace Colada;

/**
 * ArrayIterator backend for PairMap. Best for maps with only scalar keys.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class ArrayIteratorPairs extends CollectionMapIterator implements Pairs, Arrayable
{
    /**
     * @var \ArrayIterator
     */
    private $map;

    /**
     * @param \ArrayIterator $map
     */
    public function __construct(\ArrayIterator $map)
    {
        $this->map = $map;

        parent::__construct(
            $this->map,
            function($element) use($map) {
                return array($map->key(), $element);
            }
        );
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->map);
    }

    /**
     * @param mixed $key
     *
     * @return Option
     */
    public function getElementByKey($key)
    {
        if (!is_scalar($key)) {
            return new None();
        }

        return isset($this->map[$key]) ? new Some($this->map[$key]) : new None();
    }

    /**
     * @todo Use in keys set.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function containsKey($key)
    {
        if (!is_scalar($key)) {
            return false;
        }

        return isset($this->map[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->map->getArrayCopy();
    }
}
