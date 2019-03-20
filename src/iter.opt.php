<?php

namespace Colada\iter\opt;

/*
 * https://www.scala-lang.org/api/current/scala/collection/immutable/Map.html
 */

use ArrayAccess;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * Because you cannot write: $b = $a ?: throw new \InvalidArgumentException();
 *
 * @param mixed $key
 * @param ArrayAccess|array $map
 *
 * @return Option
 */
function get($key, $map): Option
{
    if (is_array($map)) {
        return array_key_exists($key, $map) ? new Some($map[$key]) : None::create();
    }

    return $map->offsetExists($key) ? new Some($map[$key]) : None::create();
}

function head(iterable $iter): Option
{
    foreach ($iter as $value) {
        return new Some($value);
    }

    return None::create();
}

function last(iterable $iter): Option
{
    $value = null;
    $iterated = false;
    foreach ($iter as $value) {
        $iterated = true;
    }

    return $iterated ? new Some($value) : None::create();
}

function find_one(callable $p, iterable $iter): Option
{
    foreach ($iter as $key => $value) {
        if ($p($value, $key)) {
            return new Some($value);
        }
    }

    return None::create();
}
