<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class None extends Option
{
    /**
     * {@inheritDoc}
     */
    public function isEqualTo($none)
    {
        return (is_object($none) && ($none instanceof static));
    }

    /**
     * {@inheritDoc}
     */
    public function isDefined()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function flatMapBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mapBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function acceptBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function eachBy($processor)
    {
        Contracts::ensureCallable($processor);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function orNull()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function orElse($else)
    {
        if (is_callable($else)) {
            return call_user_func($else);
        } else {
            return $else;
        }
    }

    /**
     * @throws \Exception
     *
     * @param \Exception|string $else
     *
     * @return mixed
     */
    public function orThrow($exception)
    {
        if (is_string($exception)) {
            $exception = new \RuntimeException($exception);
        }

        throw $exception;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator(array());
    }
}
