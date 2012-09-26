<?php

namespace Colada;

/**
 * Optional value.
 *
 * @todo Links to examples.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
abstract class Option implements \IteratorAggregate, Equalable, \Countable
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
     *
     * @return Option Self (unmodified).
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
    public function orThrow($exception)
    {
        if (is_scalar($exception)) {
            $exception = new \RuntimeException($exception);
        }

        if (!($exception instanceof \Exception)) {
            throw new \InvalidArgumentException('Invalid argument.');
        }

        return $this->orElse(function() use($exception) { throw $exception; });
    }

    /**
     * @deprecated Use orThrow() instead.
     *
     * @throws \Exception
     *
     * @param \Exception|string $exception
     *
     * @return mixed
     */
    public function orException($exception)
    {
        return $this->orThrow($exception);
    }

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

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this
            ->mapBy(function($some) {
                return new \ArrayIterator(array($some));
            })
            ->orElse(new \EmptyIterator());
    }

    /**
     * @return int
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }
}
