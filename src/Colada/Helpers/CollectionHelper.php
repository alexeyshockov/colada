<?php

namespace Colada\Helpers;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @internal
 */
class CollectionHelper
{
    /**
     * @param mixed $value
     * @param mixed $collection
     *
     * @return bool
     */
    public static function in($value, $collection)
    {
        if (!(is_object($collection) && ($collection instanceof \Colada\Collection))) {
            $collection = (new \Colada\CollectionBuilder())->addAll($collection)->build();
        }

        return $collection->contains($value);
    }
}
