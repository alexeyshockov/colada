<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
// TODO Implement Equalable?
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
     * @param $key
     *
     * @return mixed
     *
     * @throw \InvalidArgumentException
     */
    function apply($key);
}
