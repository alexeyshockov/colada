<?php

namespace Colada\Helpers;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
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

    public function isArray($value)
    {
        return is_array($value);
    }

    /**
     * @param mixed                   $object
     * @param string|\ReflectionClass $class
     *
     * @return bool
     */
    public static function isInstanceOf($object, $class)
    {
        // TODO Validate $class.

        if (!is_object($class)) {
            $class = new \ReflectionClass($class);
        }

        if (is_object($object)) {
            return $class->isInstance($object);
        } else {
            return false;
        }
    }

    public function isTraversable($value)
    {
        return (is_object($value) && ($value instanceof \Traversable));
    }
}
