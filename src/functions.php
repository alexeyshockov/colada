<?php

namespace Colada;

use Closure;
use DateTimeInterface;
use Traversable;
use Iterator;
use IteratorAggregate;
use Generator;
use IteratorIterator;
use ArrayIterator;
use PhpOption\Some;

/**
 * @param mixed ...$values
 *
 * @return Sequence
 */
function seq(...$values)
{
    return new Sequence($values);
}

/**
 * @param mixed ...$values
 *
 * @return HashSet
 */
function hash_set(...$values)
{
    return new HashSet($values);
}

/**
 * @param array ...$tuples
 *
 * @return HashMap
 */
function hash_map(array ...$tuples)
{
    return HashMap::fromTuples($tuples);
}

/**
 * @param array ...$tuples
 *
 * @return ArrayMap
 */
function array_map(array ...$tuples)
{
    return ArrayMap::fromTuples($tuples);
}

/**
 * @param mixed $host
 * @param callable $f
 *
 * @return LazyIterator
 */
function unfold($host, callable $f)
{
    return LazyIterator::fromResult(function () use ($host, $f) {
        $values = $f($host);
        while (is_object($values) && ($values instanceof Some)) {
            list ($v, $host) = $values->get();

            yield $v;

            $values = $f($host);
        }
    });
}

/**
 * @param mixed $host
 * @param callable $f
 *
 * @return LazyIterator
 */
function unfold_tuples($host, callable $f)
{
    return LazyIterator::fromResult(function () use ($host, $f) {
        $values = $f($host);
        while (is_object($values) && ($values instanceof Some)) {
            list($kv, $host) = $values->get();
            list($k, $v) = $kv;

            yield $k => $v;

            $values = $f($host);
        }
    });
}

/**
 * Popular example of generators
 *
 * @param int $low
 * @param int $high
 * @param int $step
 *
 * @return Traversable
 */
function xrange($low, $high, $step = 1)
{
    for ($i = $low; $i <= $high; $i += $step) {
        yield $i;
    }
}

/**
 * @param callable $predicate
 *
 * @return Closure
 */
function not(callable $predicate)
{
    return function (...$arguments) use ($predicate) {
        return !$predicate(...$arguments);
    };
}

const as_iterator = '\\Colada\\as_iterator';

/**
 * @param Traversable|array $t
 *
 * @return Iterator
 */
function as_iterator($t)
{
    InvalidArgumentException::assertTraversable($t);

    if (is_array($t)) {
        $i = new ArrayIterator($t);
    } else {
        if ($t instanceof IteratorAggregate) {
            $i = $t->getIterator();
        } elseif ($t instanceof Iterator) {
            $i = $t;
        } else {
            $i = new IteratorIterator($t);
            /*
             * Without explicit rewinding iterator will be not initialized.
             *
             * Example:
             * $ psysh
             * >>> $dp = new DatePeriod('R6/2000-01-01T00:00:00Z/P1D')
             * => DatePeriod {#177
             *      ...
             *    }
             * >>> $i = new IteratorIterator($dp)
             * => IteratorIterator {#184}
             * >>> $i->valid()
             * => false
             * >>> $i->rewind()
             * => null
             * >>> $i->valid()
             * => true
             */
            $i->rewind();
        }
    }

    return $i;
}

const as_generator = '\\Colada\\as_generator';

/**
 * Lazy generator for passed traversable value
 *
 * @param Traversable|array $t
 *
 * @return Generator
 */
function as_generator($t)
{
    $i = as_iterator($t);

    // To prevent rewinding in foreach.
    while ($i->valid()) {
        yield $i->key() => $i->current();

        $i->next();
    }
}

const as_tuples_generator = '\\Colada\\as_tuples_generator';

/**
 * Lazy generator of tuples (key, value) for passed traversable value
 *
 * @param Traversable|array $t
 *
 * @return Generator
 */
function as_tuples_generator($t)
{
    $i = as_iterator($t);

    // To prevent rewinding in foreach.
    while ($i->valid()) {
        yield [$i->key(), $i->current()];

        $i->next();
    }
}

const datetime_hash = '\\Colada\\datetime_hash';

/**
 * Hash function for value objects with DateTimeInterface
 *
 * @param DateTimeInterface $object
 *
 * @return int
 */
function datetime_hash(DateTimeInterface $object)
{
    // TODO Append class name?..
    return $object->format(
        // With nanoseconds, they may be available.
        'Y-m-d\TH:i:s.u'
    );
}

const any_hash = '\\Colada\\any_hash';

/**
 * @param mixed $v
 *
 * @return string
 */
function any_hash($v)
{
    if (is_scalar($v) || ($v === null)) {
        $hash = md5('('.gettype($v).') '.$v);
    } elseif (is_object($v)) {
        $hash = spl_object_hash($v);
    } elseif (is_array($v)) {
        $hash = md5('('.gettype($v).') '.json_encode($v));
    } elseif (is_resource($v)) {
        $hash = md5('('.gettype($v).') ('.get_resource_type($v).') '.$v);
    } else {
        throw new InvalidArgumentException('Unsupported type.');
    }

    return $hash;
}
