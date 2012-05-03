<?php

namespace Colada\Helpers;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @internal
 */
class TypeHelper
{
    public static function isNull($value)
    {
        return is_null($value);
    }

    public static function isScalar($value)
    {
        return is_scalar($value);
    }

    public static function isFalse($value)
    {
        return (false == $value);
    }

    public static function isTrue($value)
    {
        return (true == $value);
    }

    public function isTraversable($value)
    {
        return (is_object($value) && ($value instanceof \Traversable));
    }
}
