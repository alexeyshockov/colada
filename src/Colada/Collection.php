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
     * @return bool
     */
    function isEmpty();

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
     *
     * @return bool
     */
    function isNoneMatchBy($matcher);

    /**
     * @param mixed $element
     *
     * @return bool
     */
    function contains($element);

    /**
     * @throws \InvalidArgumentException
     *
     * @param int $offset
     * @param int $length
     *
     * @return \Colada\Collection
     */
    function slice($offset, $length);

    /**
     * A convenient version of what is perhaps the most common use-case for map: extracting a list of property values.
     *
     * @param mixed $key
     *
     * @return \Colada\Collection
     */
    function pluck($key);

    /**
     * Applies $processor to each element.
     *
     * @param callback $processor
     */
    function eachBy($processor);

    /**
     * Return first element from collection, that match $filter.
     *
     * @param callback $filter
     *
     * @return mixed
     */
    function findBy($filter);

    /**
     * Constructs new collection with elements, for which $filter returns false.
     *
     * Lazy.
     *
     * @param callback $filter
     *
     * @return \Colada\Collection
     */
    function acceptBy($filter);

    /**
     * Constructs new collection with elements, for which $filter returns false.
     *
     * Lazy.
     *
     * @param callback $filter
     *
     * @return \Colada\Collection
     */
    function rejectBy($filter);

    /**
     * Applies $mapper to each element of collection and constructs new one with results.
     *
     * Lazy.
     *
     * @param callback|mixed $mapper
     *
     * @return \Colada\Collection
     */
    function mapBy($mapper);

    /**
     * Applies $mapper to each element of collection and constructs new one with results (which must be collections of
     * new elements for each original element).
     *
     * Lazy.
     *
     * @see http://www.scala-lang.org/api/current/scala/collection/immutable/Set.html
     *
     * @param callback|\Traversable $mapper
     *
     * @return \Colada\Collection
     */
    function flatMapBy($mapper);

    /**
     * @param callback $folder
     * @param mixed    $accumulator
     *
     * @return mixed
     */
    function foldBy($folder, $accumulator);

    /**
     * Good introduction to reduce: {@link http://www.codecommit.com/blog/scala/scala-collections-for-the-easily-bored-part-2}.
     *
     * @todo Own exception class.
     *
     * @throws \Exception On empty collection.
     *
     * @param callback $reducer
     *
     * @return mixed
     */
    function reduceBy($reducer);

    /**
     * Divides collection into two parts, depending on $partitioner result for each element (boolean).
     *
     * @param callback $partitioner
     *
     * @return \Colada\Collection[] Array with two elements. Suitable for PHP's list().
     */
    function partitionBy($partitioner);

    /**
     * @param callback|null $unzipper
     *
     * @return \Colada\Collection[] Array with two elements. Suitable for PHP's list().
     */
    function unzip($unzipper = null);

    /**
     * Zips two collections into one.
     *
     * Example:
     * <code>
     * $collection1 = (new CollectionBuilder())->addAll(array("Alice", "Bob", "Joe"))->build();
     * $collection2 = (new CollectionBuilder())->addAll(array(1, 2, 3))->build();
     *
     * $pairs = $collection1->zip($collection2)->toArray();
     * // array(
     * //     array('Alice', 1),
     * //     array('Bob', 2),
     * //     array('Joe', 3),
     * // )
     * </code>
     *
     * P.S. Haskell's and Scala's zipWith() may be implemented in two steps: 1. zip(), 2. mapBy().
     *
     * @todo Lazy. With CollectionZipIterator.
     *
     * @param \Colada\Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return \Colada\Collection
     */
    function zip($collection);

    /**
     * Constructs set with elements which are in <i>either</i> sets (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Union will be (1, 2, 3, 4, 5).
     *
     * @param \Colada\Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return \Colada\Collection
     */
    function union($collection);

    /**
     * Constructs set with elements which are in <i>both</i> sets (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Intersection will be (3).
     *
     * @todo Rename to "intersection"?
     *
     * @param \Colada\Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return \Colada\Collection
     */
    function intersect($collection);

    /**
     * Constructs set with elements which lie <i>outside</i> of a current collection and within another
     * collection (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Complement will be (4, 5).
     *
     * P.S. diff() is alias in other languages and libraries.
     *
     * @param \Colada\Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return \Colada\Collection
     */
    function complement($collection);

    /**
     * Is passed collection contains all elements from this one?
     *
     * @param \Colada\Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return boolean
     */
    function isPartOf($collection);

    /**
     * @param callback $comparator
     *
     * @return \Colada\Collection
     */
    function sortBy($comparator);

    /**
     * Group each collection element in map with related key.
     *
     * @param callback $keyFinder
     *
     * @return \Colada\Map
     */
    function groupBy($keyFinder);

    /**
     * @return array
     */
    function toArray();

    /**
     * Constructs new collection with elements from current one.
     *
     * Useful, for example, to "freeze" collections from Map::asKeys() or Map::asElements().
     *
     * @return \Colada\Collection
     */
    function __clone();
}
