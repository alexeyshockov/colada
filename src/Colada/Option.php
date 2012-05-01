<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
abstract class Option implements \IteratorAggregate, Equalable
{
    /**
     * @param callable $mapper
     *
     * @return Option
     */
    abstract public function flatMapBy(callable $mapper);

    /**
     * @param callable $mapper
     *
     * @return Option
     */
    abstract public function mapBy(callable $mapper);

    /**
     * @param callable $filter
     *
     * @return Option
     */
    abstract public function filterBy(callable $filter);

    /**
     * @param callable $processor
     */
    abstract public function eachBy(callable $processor);

    /**
     * @param $else
     *
     * @return mixed
     */
    abstract public function orElse($else);

    /**
     * @return mixed
     */
    abstract public function orNull();

    /**
     * @return bool
     */
    abstract public function isDefined();

    /**
     * @param mixed $data
     *
     * @return Option
     */
    public static function from($data)
    {
        return (is_null($data) ? new None() : new Some($data));
    }

    public function __toString()
    {
        return (string) $this->orNull();
    }
}
