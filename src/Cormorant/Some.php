<?php

namespace Cormorant;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Some extends Option
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function isEqualTo($some)
    {
        return (is_object($some) && ($some instanceof static) && ComparisonHelper::isEquals($this->get(), $some->get()));
    }

    public function isDefined()
    {
        return true;
    }

    public function flatMapBy(callable $mapper)
    {
        // Grecefully handle not Option return value.
        if (!(($option = $mapper($this->data)) instanceof Option)) {
            $option = Option::from($option);
        }

        return $option;
    }

    public function mapBy(callable $mapper)
    {
        return new static($mapper($this->data));
    }

    public function filterBy(callable $filter)
    {
        if ($filter($this->data)) {
            return $this;
        } else {
            return new None();
        }
    }

    public function eachBy(callable $processor)
    {
        $processor($this->data);
    }

    public function orNull()
    {
        return $this->data;
    }

    public function orElse($else)
    {
        return $this->data;
    }

    public function get()
    {
        return $this->data;
    }

    public function getIterator()
    {
        return new \ArrayIterator(array($this->data));
    }
}
