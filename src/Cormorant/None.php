<?php

namespace Cormorant;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class None extends Option
{
    public function isEqualTo($none)
    {
        return (is_object($none) && ($none instanceof static));
    }

    public function isDefined()
    {
        return false;
    }

    public function flatMapBy(callable $mapper)
    {
        return $this;
    }

    public function mapBy(callable $mapper)
    {
        return $this;
    }

    public function filterBy(callable $filter)
    {
        return $this;
    }

    public function eachBy(callable $processor)
    {

    }

    public function orNull()
    {
        return null;
    }

    public function orElse($else)
    {
        if (is_callable($else)) {
            return $else();
        } else {
            return $else;
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator(array());
    }
}
