<?php

namespace Colada\Helpers;

use Colada\ComparisonHelper as Comparator;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class ComparisonHelper
{
    /**
     * @param mixed $value1
     * @param mixed $value2
     *
     * @return bool
     */
    public static function isEqualTo($value1, $value2)
    {
        return Comparator::isEquals($value1, $value2);
    }
}
