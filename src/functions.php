<?php

use Colada\CollectionBuilder,
    Colada\SetBuilder,
    Colada\MapBuilder,
    Colada\RangeIterator;

/**
 * xrange() from Python. Generator.
 *
 * Like range(), but instead of returning a list, returns an object that generates the numbers in the
 * range on demand.  For looping, this is slightly faster than range() and more memory efficient.
 *
 * Examples:
 * <code>
 * foreach (xrange(10) as $number) {
 *     echo $number.' ';
 * }
 * // 0 1 2 3 4 5 6 7 8 9 10
 * </code>
 *
 * <code>
 * // Infinity sequence.
 * foreach (xrange(10, null) as $number) {
 *     if (($number % 20) == 0) {
 *         break;
 *     } else {
 *         echo $number.' ';
 *     }
 * }
 * // 10 11 12 13 14 15 16 17 18 19
 * </code>
 *
 * @param int  $start
 * @param null $stop
 * @param int  $step
 *
 * @return \Colada\RangeIterator
 */
function xrange($start = 0, $stop = null, $step = 1)
{
    return new IteratorCollection(new RangeIterator($start, $stop, $step));
}

/**
 * Some useful examples:
 *
 * <code>
 * $collection->acceptBy(x()->getName()->startsWith('Test'));
 * </code>
 *
 * vs.
 *
 * <code>
 * $collection->acceptBy(
 *     function($user) { return StringHelper::startsWith($user->getName(), 'Test'); }
 * );
 * </code>
 *
 * @todo Check with function_exists().
 *
 * @return \Colada\X\FutureValue
 */
function x()
{
    return new \Colada\X\FutureValue();
}



/**
 * @todo Check with function_exists().
 *
 * Like array(). Example:
 *
 * <code>
 * $collection = collection(1, 2, 3);
 * </code>
 *
 * @return \Colada\Collection
 */
function collection()
{
    $builder = new CollectionBuilder();

    return $builder->addAll(func_get_args())->build();
}

/**
 * @todo Check with function_exists().
 *
 * Like array(). Example:
 *
 * <code>
 * $set = set(1, 2, 3);
 * </code>
 *
 * @return \Colada\Collection
 */
function set($data = array())
{
    $builder = new SetBuilder();

    return $builder->addAll(func_get_args())->build();
}



/**
 * @param array|\Traversable|mixed $data
 *
 * @return \Colada\Collection
 */
function to_collection($data)
{
    $builder = new CollectionBuilder();

    return $builder->addAll($data)->build();
}

/**
 * @param array|\Traversable|mixed $data
 *
 * @return \Colada\Collection
 */
function to_set($data)
{
    $builder = new SetBuilder();

    return $builder->addAll($data)->build();
}

/**
 * @param array|\Traversable $data
 *
 * @return \Colada\Map
 */
function to_map($data)
{
    $builder = new MapBuilder();

    // TODO MapBuilder::putAll()?
    foreach ($data as $key => $element) {
        $builder->put($key, $element);
    }

    return $builder->build();
}



/**
 * P.S. to_set() will be equal to this function. Not needed.
 */
function as_collection(\Traversable $collection)
{
    return new \IteratorCollection($collection);
}
