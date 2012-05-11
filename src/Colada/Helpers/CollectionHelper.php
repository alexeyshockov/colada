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
     * @param \Colada\Collection|mixed $collection
     *
     * @return bool
     */
    public static function in($value, $collection)
    {
        if (!(is_object($collection) && ($collection instanceof \Colada\Collection))) {
            $builder = new \Colada\CollectionBuilder();

            $collection = $builder->addAll($collection)->build();
        }

        return $collection->contains($value);
    }

    /**
     * @param array|\ArrayAccess|\Colada\Map $value
     * @param mixed $key
     *
     * @return Option
     */
    public static function at($value, $key)
    {
        // TODO Ensure type...

        if (is_object($value) && ($value instanceof \Colada\Map)) {
            return $value->get($key);
        } else {
            return (is_scalar($key) && isset($value[$key]) ? new \Colada\Some($value[$key]) : new \Colada\None());
        }
    }
}
