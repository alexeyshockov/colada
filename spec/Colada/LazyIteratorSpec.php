<?php

namespace spec\Colada;

// PHPSpec will fail without this.
require_once 'IterableSpecLike.php';

use ArrayIterator;
use EmptyIterator;
use DateTime;
use DatePeriod;
use Colada\LazyIterator;
use PhpSpec\ObjectBehavior;
use PhpOption\None;
use PhpOption\Some;

/**
 * @mixin LazyIterator
 */
class LazyIteratorSpec extends ObjectBehavior
{
    use IterableSpecLike;

    function let()
    {
        $this->beConstructedWith(new ArrayIterator([1, 2, 3, 4, 5, 6]));
    }

    function it_can_be_created_from_result()
    {
        $source = [1, 2, 3];
        $this->beConstructedFromResult(function () use ($source) {
            return new ArrayIterator($source);
        });

        $this->shouldContains($source);
    }






    function it_can_be_mapped()
    {
        $result = $this->map(function ($v, $k) { return $v * 2; });

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([2, 4, 6, 8, 10, 12]);
    }

    function it_can_be_flatten()
    {
        $this->beConstructedWith(new ArrayIterator([[1, 2], [3, 4], [5, 6]]));

        $result = $this->flatten();

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([1, 2, 3, 4, 5, 6]);
    }

    function it_can_be_binded()
    {
        $this->beConstructedWith(new ArrayIterator([1, 3, 5]));

        $result = $this->flatMap(function ($v, $k) { return [$v, $v + 1]; });

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([1, 2, 3, 4, 5, 6]);
    }

    // TODO Generalize.
    function it_can_be_sorted()
    {
        $this->beConstructedWith(new ArrayIterator([6, 4, 5, 2, 3, 1]));

        $result = $this->sort();

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldBeSortedByValues();
    }






    function it_can_be_filtered()
    {
        $isEven = function ($v, $k) { return $v % 2 === 0; };

        $selected = $this->select($isEven);
        $selected->shouldHaveType(LazyIterator::class);
        $selected->shouldContains([1 => 2, 3 => 4, 5 => 6]);

        $rejected = $this->reject($isEven);
        $rejected->shouldHaveType(LazyIterator::class);
        $rejected->shouldContains([0 => 1, 2 => 3, 4 => 5]);
    }

    function it_can_drop_first_tuples()
    {
        $result = $this->drop(3);

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([3 => 4, 4 => 5, 5 => 6]);
    }

    function it_can_take_only_first_tuples()
    {
        $result = $this->take(3);

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([1, 2, 3]);
    }

    function it_can_be_zipped_with_array()
    {
        $this->beConstructedWith(new ArrayIterator([1, 2, 3]));

        $zipped = $this->zip(['one', 'two', 'three']);

        $zipped->shouldHaveType(LazyIterator::class);
        $zipped->shouldContainsValues([[1, 'one'], [2, 'two'], [3, 'three']]);
    }

    function it_can_be_zipped_with_traversable_object()
    {
        $this->beConstructedWith(new ArrayIterator(['Saturday', 'Sunday', 'Monday']));

        $zipped = $this->zip(new DatePeriod('R2/2000-01-01T00:00:00Z/P1D'));

        $zipped->shouldHaveType(LazyIterator::class);
        // TODO Show keys too.
        $zipped->shouldContainsValues([
            ['Saturday', new DateTime('2000-01-01T00:00:00+00:00')],
            ['Sunday', new DateTime('2000-01-02T00:00:00+00:00')],
            ['Monday', new DateTime('2000-01-03T00:00:00+00:00')],
        ]);
    }

    function it_should_produce_option_for_first_value_if_its_available()
    {
        $this->first()->shouldBeLike(new Some(1));
    }

    function it_should_produce_option_for_first_value_if_its_not_available()
    {
        $this->beConstructedWith(new EmptyIterator());

        $this->first()->shouldBe(None::create());
    }

    function it_should_produce_option_for_last_value_if_its_available()
    {
        $this->last()->shouldBeLike(new Some(6));
    }

    function it_should_produce_option_for_last_value_if_its_not_available()
    {
        $this->beConstructedWith(new EmptyIterator());

        $this->last()->shouldBe(None::create());
    }

    function it_can_be_transformed_to_array()
    {
        $this->toArray()->shouldBe([1, 2, 3, 4, 5, 6]);
    }

    function it_can_perform_an_action_for_all_tuples()
    {
        // TODO Implement.
    }

    function it_can_reindex_values()
    {
        $grouped = $this->reindex(function ($v, $k) { return $v % 2; });

        $grouped->shouldHaveType(LazyIterator::class);
        $grouped->shouldContainsTuples([[1, 1], [0, 2], [1, 3], [0, 4], [1, 5], [0, 6]]);

        // After that $grouped can be transformed to a map (ArrayMap or HashMap).
    }

    function it_can_be_folded()
    {
        $this
            // The sum of values.
            ->foldLeft(0, function ($result, $v, $k) { return $result + $v;})
            ->shouldBe(21);
    }

    function it_can_be_reversed()
    {
        $this->reversed()->shouldContains([5 => 6, 4 => 5, 3 => 4, 2 => 3, 1 => 2, 0 => 1]);
    }

    function it_should_be_empty_without_any_content()
    {
        $this->beConstructedWith(new EmptyIterator());

        $this->isEmpty()->shouldBe(true);
    }

    function it_should_not_be_empty_with_content()
    {
        $this->isEmpty()->shouldBe(false);
    }

    function it_should_produce_first_matched_value_option_for_successful_search()
    {
        $this->find(function ($v, $k) { return $v > 3; })->shouldBeLike(new Some(4));
    }

    function it_should_produce_empty_option_for_failed_search()
    {
        $this->find(function ($v, $k) { return $v < 0; })->shouldBe(None::create());
    }

    function it_should_contain_existing_value()
    {
        $this->contains(1)->shouldBe(true);
    }

    function it_should_not_contain_not_existing_value()
    {
        $this->contains(7)->shouldBe(false);
    }

    function it_should_contain_existing_key()
    {
        $this->containsKey(0)->shouldBe(true);
    }

    function it_should_not_contain_not_existing_key()
    {
        $this->containsKey(6)->shouldBe(false);
    }

    function it_should_be_available_as_tuples()
    {
        $tuples = $this->asTuples();

        $tuples->shouldHaveType(LazyIterator::class);
        $tuples->shouldContains([
            [0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]
        ]);
    }
}
