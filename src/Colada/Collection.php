<?php

namespace Colada;

/**
 * @todo Implement Equalable?
 *
 * General collection interface (immutable). Currently one for all collection types (lists, sets).
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface Collection
{
    /**
     * @param mixed $element
     *
     * @return bool
     */
    function contains($element);

    /**
     * @return bool
     */
    function isEmpty();

    /**
     * @return array
     */
    function toArray();

    /**
     * Constructs new collection with elements, for which $filter returns false.
     *
     * @param callback $filter
     *
     * @return Collection
     */
    function acceptBy($filter);

    /**
     * Constructs new collection with elements, for which $filter returns false.
     *
     * @param callback $filter
     *
     * @return Collection
     */
    function rejectBy($filter);

    /**
     * Return first element from collection, that match $filter.
     *
     * @param callback $filter
     *
     * @return mixed
     */
    function findBy($filter);

    /**
     * Applies $mapper to each element of collection and constructs new one with results.
     *
     * @param callback $mapper
     *
     * @return Collection
     */
    function mapBy($mapper);

    /**
     * Applies $mapper to each element of collection and constructs new one with results (which must be collections of
     * new elements for each original element).
     *
     * @param callback $mapper
     *
     * @return Collection
     */
    function flatMapBy($mapper);

    /**
     * Group each collection element in map with related key.
     *
     * @param callback $keyFinder
     *
     * @return Map
     */
    function groupBy($keyFinder);

    /**
     * Divides collection into two parts, depending on $partitioner result for each element (boolean).
     *
     * @param callback $partitioner
     *
     * @return Collection Of two elements.
     */
    function partitionBy($partitioner);

    /**
     * @param callback $matcher
     *
     * @return bool
     */
    function isAnyMatchBy($matcher);

    /**
     * @param callback $matcher
     *
     * @return bool
     */
    function isAllMatchBy($matcher);

    /**
     * @param callback $matcher
     *}
     * @return bool
     */
    function isNoneMatchBy($matcher);

    /**
     * @throws \InvalidArgumentException
     *
     * @param int $offset
     * @param int $length
     *
     * @return Collection
     */
    function slice($offset, $length);

    /**
     * A convenient version of what is perhaps the most common use-case for map: extracting a list of property values.
     *
     * @param mixed $key
     *
     * @return Collection
     */
    function pluck($key);

    /**
     * Applies $processor to each element.
     *
     * @param callback $processor
     */
    function eachBy($processor);
}
