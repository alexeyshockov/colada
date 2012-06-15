<?php

use Colada\CollectionBuilder,
    Colada\SetBuilder,
    Colada\MapBuilder,
    Colada\MultimapBuilder;

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
