<?php

namespace spec\Colada;

// PHPSpec will fail without this.
require_once 'IterableSpecLike.php';

use ArrayObject;
use Countable;
use JsonSerializable;
use DateTime;
use DatePeriod;
use Colada\ArrayMap;
use Colada\LazyIterator;
use PhpSpec\ObjectBehavior;
use PhpOption\None;
use PhpOption\Some;

/**
 * @mixin ArrayMap
 */
class ArrayMapSpec extends ObjectBehavior
{
    use IterableSpecLike;

    function let()
    {
        $this->beConstructedWith([1, 2, 3, 4, 5, 6]);
    }

    function it_can_be_created_empty()
    {
        $this->beConstructedWith();
        $this->shouldBeEmpty();
    }

    function it_can_wrap_array_object()
    {
        $source = [1, 2, 3];
        $arrayObject = new ArrayObject($source);

        $this->beConstructedThrough('wrap', [$arrayObject]);

        $this->shouldContains($source);
    }

    function it_can_be_created_from_result()
    {
        $source = [1, 2, 3];
        $this->beConstructedFromResult(function () use ($source) {
            return $source;
        });

        $this->shouldContains($source);
    }






    function it_can_be_mapped()
    {
        $result = $this->map(function ($v, $k) { return $v * 2; });

        $result->shouldHaveType(ArrayMap::class);
        $result->shouldContains([2, 4, 6, 8, 10, 12]);
    }

    function it_can_be_flatten()
    {
        $this->beConstructedWith([[1, 2], [3, 4], [5, 6]]);

        $result = $this->flatten();

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([1, 2, 3, 4, 5, 6]);
    }

    function it_can_be_binded()
    {
        $this->beConstructedWith([1, 3, 5]);

        $result = $this->flatMap(function ($v, $k) { return [$v, $v + 1]; });

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldContains([1, 2, 3, 4, 5, 6]);
    }

    // TODO Generalize.
    function it_can_be_sorted()
    {
        $this->beConstructedWith([6, 4, 5, 2, 3, 1]);

        $result = $this->sort();

        $result->shouldHaveType(LazyIterator::class);
        $result->shouldBeSortedByValues();
    }

    function it_should_produce_independent_iterator()
    {
        $i1 = $this->getIterator();
        $i2 = $this->getIterator();

        $i1->shouldHaveType(LazyIterator::class);
        $i2->shouldHaveType(LazyIterator::class);

        $i1->shouldBeIndependentFrom($i2);
    }






    function it_can_be_filtered()
    {
        $isEven = function ($v, $k) { return $v % 2 === 0; };

        $selected = $this->select($isEven);
        $selected->shouldHaveType(ArrayMap::class);
        $selected->shouldContains([1 => 2, 3 => 4, 5 => 6]);

        $rejected = $this->reject($isEven);
        $rejected->shouldHaveType(ArrayMap::class);
        $rejected->shouldContains([0 => 1, 2 => 3, 4 => 5]);
    }

    function it_can_drop_first_tuples()
    {
        $result = $this->drop(3);

        $result->shouldHaveType(ArrayMap::class);
        $result->shouldContains([3 => 4, 4 => 5, 5 => 6]);
    }

    function it_can_take_only_first_tuples()
    {
        $result = $this->take(3);

        $result->shouldHaveType(ArrayMap::class);
        $result->shouldContains([1, 2, 3]);
    }

    function it_can_be_zipped_with_array()
    {
        $this->beConstructedWith([1, 2, 3]);

        $zipped = $this->zip(['one', 'two', 'three']);

        $zipped->shouldHaveType(LazyIterator::class);
        $zipped->shouldContainsValues([[1, 'one'], [2, 'two'], [3, 'three']]);
    }

    function it_can_be_zipped_with_traversable_object()
    {
        $this->beConstructedWith(['Saturday', 'Sunday', 'Monday']);

        $zipped = $this->zip(new DatePeriod('R2/2000-01-01T00:00:00Z/P1D'));

        $zipped->shouldHaveType(LazyIterator::class);
        // TODO Show keys too.
        $zipped->shouldContainsValues([
            ['Saturday', new DateTime('2000-01-01T00:00:00+00:00')],
            ['Sunday', new DateTime('2000-01-02T00:00:00+00:00')],
            ['Monday', new DateTime('2000-01-03T00:00:00+00:00')],
        ]);
    }

    function it_can_be_unzipped()
    {
        $this->beConstructedWith([['Africa', 'bananas'], ['Europa', 'potatoes'], ['Asia', 'rice']]);

        // TODO Implement.
    }

    function it_can_be_unzipped_with_custom_function()
    {
        $this->beConstructedWith([['Africa', 'bananas'], ['Europa', 'potatoes'], ['Asia', 'rice']]);

        $result = $this->unzip(function ($v, $k) {
            list($continent, $food) = $v;

            yield $continent[0] => $continent;
            yield $food[0] => $food;
        });
    }

    function it_should_produce_option_for_first_value_if_its_available()
    {
        $this->first()->shouldBeLike(new Some(1));
    }

    function it_should_produce_option_for_first_value_if_its_not_available()
    {
        $this->beConstructedWith();

        $this->first()->shouldBe(None::create());
    }

    function it_should_produce_option_for_last_value_if_its_available()
    {
        $this->last()->shouldBeLike(new Some(6));
    }

    function it_should_produce_option_for_last_value_if_its_not_available()
    {
        $this->beConstructedWith();

        $this->last()->shouldBe(None::create());
    }

    function it_is_countable()
    {
        $this->shouldHaveType(Countable::class);
        $this->count()->shouldBe(6);
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
        $this->beConstructedWith([1, 2, 3]);
        $grouped = $this->reindex(function ($v, $k) { return $v % 2; });

        $grouped->shouldHaveType(LazyIterator::class);
        $grouped->shouldContainsTuples([[1, 1], [0, 2], [1, 3]]);

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
        $this->beConstructedWith();
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

    public function it_can_be_converted_to_JSON()
    {
        $this->shouldHaveType(JsonSerializable::class);
        $this->jsonSerialize()->shouldBe([1, 2, 3, 4, 5, 6]);
    }
}
