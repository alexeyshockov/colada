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
    public static function isEqualTo($value1, $value2)
    {
        return Comparator::isEquals($value1, $value2);
    }
}
