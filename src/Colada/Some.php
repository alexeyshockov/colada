<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Some extends Option
{
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function isEqualTo($some)
    {
        return (is_object($some) && ($some instanceof static) && ComparisonHelper::isEquals($this->get(), $some->get()));
    }

    /**
     * {@inheritDoc}
     */
    public function isDefined()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function flatMapBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        // Grecefully handle not Option return value.
        if (!(($option = call_user_func($mapper, $this->data)) instanceof Option)) {
            $option = Option::from($option);
        }

        return $option;
    }

    /**
     * {@inheritDoc}
     */
    public function mapBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return new static(call_user_func($mapper, $this->data));
    }

    /**
     * {@inheritDoc}
     */
    public function acceptBy($filter)
    {
        Contracts::ensureCallable($filter);

        if (call_user_func($filter, $this->data)) {
            return $this;
        } else {
            return new None();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function eachBy($processor)
    {
        Contracts::ensureCallable($processor);

        call_user_func($processor, $this->data);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function orNull()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function orElse($else)
    {
        return $this->data;
    }

    /**
     * Original value.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->data;
    }
}
