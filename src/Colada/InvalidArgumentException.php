<?php

namespace Colada;

use LogicException;
use Traversable;

class InvalidArgumentException extends LogicException
{
    /**
     * @param mixed $value
     */
    public static function assertTraversable($value)
    {
        if (!is_array($value) && !(is_object($value) && ($value instanceof Traversable))) {
            throw new static('Unsupported argument type. Only arrays and Traversable objects are supported.');
        }
    }

    /**
     * @param mixed $value
     */
    public static function assertScalar($value)
    {
        if (!is_scalar($value)) {
            throw new static('Unsupported argument type. Only scalars are supported.');
        }
    }

    /**
     * @param mixed $value
     */
    public static function assertObject($value)
    {
        if (!is_object($value)) {
            throw new static('Unsupported argument type. Only objects are supported.');
        }
    }
}
