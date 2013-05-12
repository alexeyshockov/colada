<?php

namespace Colada;

/**
 * General map interface (immutable). Supports all types of keys, not only scalars (like PHP core collections).
 *
 * @todo Implement Equalable?..
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface Map extends \JsonSerializable, \ArrayAccess, \Traversable
{
    /**
     * @return bool
     */
    function isEmpty();

    /**
     * @return \Colada\Map
     */
    function flip();

    /**
     * Elements view.
     *
     * @return \Colada\Collection
     */
    function asElements();

    /**
     * Keys view.
     *
     * @return \Colada\Collection
     */
    function asKeys();

    /**
     * @param callback $filter
     *
     * @return static
     */
    function acceptBy($filter);

    /**
     * @param callback $filter
     *
     * @return static
     */
    function rejectBy($filter);

    /**
     * @param callback $mapper
     *
     * @return \Colada\Map
     */
    function mapElementsBy($mapper);

    /**
     * @param callback $processor
     *
     * @return \Colada\Map Self.
     */
    function eachBy($processor);

    /**
     * Return a new Map, filtered to only have elements for the whitelisted keys.
     *
     * @param array|\Traversable $keys
     *
     * @return static
     */
    function pick($keys);

    /**
     * @param mixed $key
     * @param mixed $element
     *
     * @return static
     */
    function put($key, $element);

    /**
     * @param mixed $key
     *
     * @return static
     */
    function remove($key);

    /**
     * @param callback $mapper
     *
     * @return \Colada\Map|\Colada\Collection
     */
    function mapBy($mapper);

    /**
     * @param callback $mapper
     *
     * @return \Colada\Map|\Colada\Collection
     */
    function flatMapBy($mapper);

    /**
     * Pairs view.
     *
     * @return \Colada\Collection
     */
    function asPairs();

    /**
     * @param mixed $key
     *
     * @return Option
     */
    function get($key);

    /**
     * Alias for get().
     *
     * @param mixed $key
     *
     * @return mixed
     */
    function __invoke($key);

    /**
     * Return associated value or throw exception, if key not exists in map.
     *
     * @throws \OutOfBoundsException
     *
     * @param $key
     *
     * @return mixed
     */
    function apply($key);

    /**
     * @param mixed $element
     *
     * @return bool
     */
    function contains($element);

    /**
     * @param mixed $key
     *
     * @return bool
     */
    function containsKey($key);

    /**
     * @throws \DomainException If map can not be converted to native array.
     *
     * @return array
     */
    function toArray();

    /**
     * Constructs new map with keys and elements from current one.
     *
     * Useful, for example, to apply all lazy operations.
     *
     * @return static
     */
    function __clone();
}
