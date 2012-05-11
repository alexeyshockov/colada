<?php

namespace Colada;

/**
 * @todo Implement Equalable?
 *
 * General map interface (immutable). Supports all types of keys, not only scalars (like PHP core collections).
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface Map
{
    /**
     * @return bool
     */
    function isEmpty();

    /**
     * @param mixed $key
     *
     * @return bool
     */
    function containsKey($key);

    /**
     * @param mixed $element
     *
     * @return bool
     */
    function contains($element);

    /**
     * @param mixed $key
     *
     * @return Option
     */
    function get($key);

    /**
     * Return associated value or throw exception, if key not exists in map.
     *
     * @todo Right exception.
     *
     * @throws \InvalidArgumentException
     *
     * @param $key
     *
     * @return mixed
     */
    function apply($key);

    /**
     * Alias for get().
     *
     * @param mixed $key
     *
     * @return mixed
     */
    function __invoke($key);
}
