<?php

namespace Colada;

/**
 * General collection interface (immutable). Currently one for all collection types (lists (sequences), sets).
 *
 * @todo Implement Equalable?..
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface Collection extends \JsonSerializable, \Traversable
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
     * @param int $offset
     * @param int $length
     *
     * @return static
     */
    function slice($offset, $length);

    /**
     * Splits collection on given element.
     *
     * <code>
     * (1, 2, 3, 4, 5).splitAt(2) = ((1, 2), (3, 4, 5))
     * </code>
     *
     * @param mixed $element
     *
     * @return \Colada\Collection[]
     */
    function splitAt($element);

    /**
     * All elements from start to given element (including).
     *
     * <code>
     * (1, 2, 3, 4, 5).takeTo(2) = (1, 2)
     * </code>
     *
     * @param mixed $element
     *
     * @return static
     */
    function takeTo($element);

    /**
     * All elements from given element (excluding) to end.
     *
     * <code>
     * (1, 2, 3, 4, 5).dropFrom(2) = (3, 4, 5)
     * </code>
     *
     * @param mixed $element
     *
     * @return static
     */
    function dropFrom($element);

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
     *
     * @return \Colada\Collection Self.
     */
    function eachBy($processor);

    /**
     * Return first element from collection, that match $filter.
     *
     * @param callback $filter
     *
     * @return \Colada\Option
     */
    function findBy($filter);

    /**
     * Constructs new collection with elements, for which $filter returns true.
     *
     * @param callback $filter
     *
     * @return static
     */
    function acceptBy($filter);

    /**
     * Constructs new collection with elements, for which $filter returns false.
     *
     * @param callback $filter
     *
     * @return static
     */
    function rejectBy($filter);

    /**
     * Applies $mapper to each element of collection and constructs new one with results.
     *
     * @param callback|mixed $mapper
     *
     * @return \Colada\Collection
     */
    function mapBy($mapper);

    /**
     * Replace all elements, matching $filter, with $value.
     *
     * Short version of:
     * <code>
     *     $filter = function($element) { ... };
     *     $value  = '' // Some value for replace.
     *
     *     $collection->mapBy(function($element) use($filter, $value) {
     *         if ($filter($element)) {
     *             return $value;
     *         } else {
     *             return $element;
     *         }
     *     });
     * </code>
     *
     * @param callback|mixed $filter
     * @param mixed          $value
     *
     * @return static
     */
    function replace($filter, $value);

    /**
     * Process all elements, matching $filter, with $processor.
     *
     * Short version of:
     * <code>
     *     $filter    = function($element) { ... };
     *     $processor = function($element) { ... };
     *
     *     $collection->mapBy(function($element) use($filter, $processor) {
     *         if ($filter($element)) {
     *             return $processor($element);
     *         } else {
     *             return $element;
     *         }
     *     });
     * </code>
     *
     * @param callback|mixed $filter
     * @param callback       $processor
     *
     * @return static
     */
    function replaceBy($filter, $processor);

    /**
     * Applies $mapper to each element of collection and constructs new one with results (which must be collections of
     * new elements for each original element).
     *
     * @see http://www.scala-lang.org/api/current/scala/collection/immutable/Set.html
     *
     * @param callback|array|\Traversable $mapper
     *
     * @return \Colada\Collection
     */
    function flatMapBy($mapper);

    /**
     * Converts this collection of traversable collections into a collection formed by the elements of these traversable
     * collections.
     *
     * @return \Colada\Collection
     */
    function flatten();

    /**
     * @param callback $folder
     * @param mixed    $accumulator
     *
     * @return mixed
     */
    function foldBy($folder, $accumulator = null);

    /**
     * Good introduction to reduce: {@link http://www.codecommit.com/blog/scala/scala-collections-for-the-easily-bored-part-2}.
     *
     * @throws \UnderflowException On empty collections.
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
     * @return static[] Array with two elements. Suitable for PHP's list().
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
     * @return static
     */
    function union($collection);

    /**
     * Constructs set with elements which are in <i>both</i> sets (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Intersection will be (3).
     *
     * @param \Colada\Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return static
     */
    function intersection($collection);

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
     * @return static
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
     * Alias for Collection::head().
     *
     * @return \Colada\Option One head element (first element).
     */
    function first();

    /**
     * @return \Colada\Option Last element.
     */
    function last();

    /**
     * @return \Colada\Option One head element (first element).
     */
    function head();

    /**
     * @throws \UnderflowException For empty collections.
     *
     * @return static Tail — all elements, except one head element.
     */
    function tail();

    /**
     * Group each collection element in map with related key.
     *
     * @param callback $keyFinder
     * @param bool     $uniqueKeys
     *
     * @return \Colada\Map
     */
    function groupBy($keyFinder, $uniqueKeys = false);

    /**
     * Adds $elements to collection (copied from current) and return them.
     *
     * @param mixed $element
     *
     * @return static
     */
    function add($element);

    /**
     * Removes all $element from collection (copied from current) and return them.
     *
     * @deprecated Use reject() instead.
     *
     * @param mixed $element
     *
     * @return static
     */
    function remove($element);

    /**
     * @see \Colada\Collection::remove()
     *
     * @param mixed $element
     *
     * @return static
     */
    function reject($element);

    /**
     * @param mixed $delimiter Scalar or object with __toString().
     *
     * @return mixed
     */
    function join($delimiter);

    /**
     * @return array
     */
    function toArray();

    /**
     * @return \Colada\Collection
     */
    function toSet();

    /**
     * Constructs new collection with elements from current one.
     *
     * Useful, for example, to “freeze” collections from Map::asKeys() or Map::asElements().
     *
     * @return static
     */
    function __clone();
}
