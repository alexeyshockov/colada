<?php

namespace Colada\ds;

use ArrayAccess;
use ArrayObject;
use Countable;
use Ds\Map;
use Traversable;

/**
 * @api
 *
 * @param callable $groupFn
 * @param iterable $flow
 *
 * @return ArrayAccess&Traversable&Countable An instance of Ds\Map or ArrayObject
 */
function group_by(callable $groupFn, iterable $flow)
{
    $map = null;
    foreach ($flow as $key => $value) {
        $group = $groupFn($value, $key);

        if (!$map) {
            // Only once for the whole invocation
            $map = is_object($group) ? new Map() : new ArrayObject();
        }

        if (!isset($map[$group])) {
            // Only once for a group
            $map[$group] = is_object($key) ? new Map() : array();
        }

        $map[$group][$key] = $value;
    }

    return $map;
}
