<?php

namespace spec\Colada;

use Traversable;
use Iterator;
use PhpSpec\Exception\Example\FailureException;

use function Colada\as_tuples_generator;

/**
 * @mixin \Colada\IterableLike
 */
trait IterableSpecLike
{
    public function getMatchers()
    {
        return [
            'beIndependentFrom' => function (Iterator $i1, Iterator $i2) {
                $t1 = iterator_to_array(as_tuples_generator($i1));
                $t2 = iterator_to_array(as_tuples_generator($i2));

                if ($t1 !== $t2) {
                    throw new FailureException("Iterators are dependent or aren\'t equal.");
                }

                return true;
            },
            'contains' => function ($iterable, $expectedData) {
                $data = iterator_to_array(as_tuples_generator($iterable));
                $expectedData = iterator_to_array(as_tuples_generator($expectedData));

                // !== is not used to respect types. Order already controlled.
                if ($data != $expectedData) {
                    throw new FailureException("Iterable doesn\'t contain expected data.");
                }

                return true;
            },
            'containsTuples' => function ($iterable, $expectedData) {
                $data = iterator_to_array(as_tuples_generator($iterable));
                // TODO $expectedData

                // !== is not used to respect types. Order already controlled.
                if ($data != $expectedData) {
                    throw new FailureException("Iterable doesn\'t contain expected data.");
                }

                return true;
            },
            'containsKeys' => function ($iterable, array $expectedKeys) {
                $keys = [];
                foreach ($iterable as $k => $v) {
                    $keys[] = $k;
                }

                // !== is not used to respect types. Order already controlled.
                if ($keys != $expectedKeys) {
                    throw new FailureException("Iterable doesn\'t contain expected keys.");
                }

                return true;
            },
            'containsValues' => function ($iterable, $expectedValues) {
                $values = [];
                foreach ($iterable as $v) {
                    $values[] = $v;
                }

                // !== is not used to respect types. Order already controlled.
                if ($values != $expectedValues) {
                    throw new FailureException("Iterable doesn\'t contain expected values.");
                }

                return true;
            },
            'haveValues' => function ($iterable, $expectedValues) {
                $values = [];
                foreach ($iterable as $v) {
                    $values[] = $v;
                }

                if (array_diff($expectedValues, $values) !== []) {
                    throw new FailureException("Iterable doesn\'t have expected values.");
                }
                if (array_diff($values, $expectedValues) !== []) {
                    throw new FailureException('Iterable has more than expected values.');
                }

                return true;
            },
            'beEmpty' => function ($iterable) {
                $tuples = iterator_to_array(as_tuples_generator($iterable));

                if (count($tuples) !== 0) {
                    throw new FailureException("Iterable isn\'t empty.");
                }

                return true;
            },
            'beSortedByValues' => function ($iterable) {
                // TODO Support any iterable values.
                array_reduce(iterator_to_array($iterable), function ($previous, $current) {
                    if ($previous > $current) {
                        throw new FailureException("Iterable doesn't contain expected data.");
                    }
                });

                return true;
            }
        ];
    }
}
