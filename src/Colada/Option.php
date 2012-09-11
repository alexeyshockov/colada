<?php

namespace Colada;

/**
 * Optional value.
 *
 * @todo Links to examples.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
abstract class Option implements \IteratorAggregate, Equalable
{
    /**
     * @param callback $mapper
     *
     * @return Option
     */
    abstract public function flatMapBy($mapper);

    /**
     * @param callback $mapper
     *
     * @return Option
     */
    abstract public function mapBy($mapper);

    /**
     * @param callback $filter
     *
     * @return Option
     */
    abstract public function acceptBy($filter);

    /**
     * @param callback $processor
     */
    abstract public function eachBy($processor);

    /**
     * @param callback|mixed $else
     *
     * @return mixed
     */
    abstract public function orElse($else);

    /**
     * @throws \Exception
     *
     * @param \Exception|string $else
     *
     * @return mixed
     */
    abstract public function orException($exception);

    /**
     * @return mixed
     */
    abstract public function orNull();

    /**
     * Always true for {@link Some}, always false for {@link None}.
     *
     * @return bool
     */
    abstract public function isDefined();

    /**
     * None for nulls, Some for other values.
     *
     * @param mixed $data
     *
     * @return Option
     */
    public static function from($data)
    {
        return (is_null($data) ? new None() : new Some($data));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->orNull();
    }
}
