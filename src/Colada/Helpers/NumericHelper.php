<?php

namespace Colada\Helpers;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class NumericHelper
{
    /**
     * @param number $value
     *
     * @return bool
     */
    public static function isNan($value)
    {
        return is_nan($value);
    }

    /**
     * @param number $value
     *
     * @return bool
     */
    public static function isFinite($value)
    {
        return is_finite($value);
    }

    /**
     * @param number $value
     *
     * @return bool
     */
    public static function isInfinite($value)
    {
        return !is_finite($value);
    }

    /**
     * @param number $value
     *
     * @return bool
     */
    public static function isPositive($value)
    {
        return ($value >= 0);
    }

    /**
     * @param number $value
     *
     * @return bool
     */
    public static function isNegative($value)
    {
        return ($value < 0);
    }

    /**
     * @param number $value
     *
     * @return number
     */
    public static function increment($value)
    {
        return $value + 1;
    }

    /**
     * @param number $value
     *
     * @return number
     */
    public static function decrement($value)
    {
        return $value - 1;
    }
}
