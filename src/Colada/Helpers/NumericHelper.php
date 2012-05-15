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
    public function isNan($value)
    {
        return is_nan($value);
    }

    /**
     * @param number $value
     *
     * @return bool
     */
    public function isFinite($value)
    {
        return is_finite($value);
    }

    /**
     * @param number $value
     *
     * @return bool
     */
    public function isInfinite($value)
    {
        return !is_finite($value);
    }

    /**
     * @param number $value
     *
     * @return number
     */
    public function increment($value)
    {
        return $value + 1;
    }

    /**
     * @param number $value
     *
     * @return number
     */
    public function decrement($value)
    {
        return $value - 1;
    }
}
