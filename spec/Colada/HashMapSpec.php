<?php

namespace spec\Colada;

// PHPSpec will fail without this.
require_once 'IterableSpecLike.php';

use DateTime;
use SplObjectStorage;
use Colada\HashMap;
use Colada\LazyIterator;
use PhpSpec\ObjectBehavior;
use PhpOption\None;
use PhpOption\Some;

use Exception;

/**
 * @mixin HashMap
 */
class HashMapSpec extends ObjectBehavior
{
    use IterableSpecLike;

    protected function getDefaultSourceTuples()
    {
        // DateTime to timestamp.
        return [
            [new DateTime('2000-01-01 00:00:00'), 946674000],
            [new DateTime('2000-01-02 00:00:00'), 946760400]
        ];
    }

    protected function getDefaultSource()
    {
        foreach ($this->getDefaultSourceTuples() as $t) {
            yield $t[0] => $t[1];
        }
    }

    function it_can_be_created_empty()
    {
        $this->beConstructedWith();
        $this->shouldBeEmpty();
    }

    function it_can_wrap_spl_object_storage()
    {
        $source = $this->getDefaultSourceTuples();
        $objectStorage = new SplObjectStorage();
        foreach ($source as $tuple) {
            $objectStorage[$tuple[0]] = $tuple[1];
        }

        $this->beConstructedThrough('wrap', [$objectStorage]);

        $this->shouldContainsTuples($source);
    }

    function it_can_be_created_from_result_tuples()
    {
        $source = $this->getDefaultSourceTuples();
        $this->beConstructedFromResultTuples(function () use ($source) {
            return $source;
        });

        $this->shouldContainsTuples($source);
    }

    function it_should_have_spl_hasher_by_default()
    {
        $this->getHasher()->shouldBe('spl_object_hash');
    }






    function it_should_produce_independent_iterator()
    {
        $this->beConstructedWith($this->getDefaultSource());

        $i1 = $this->getIterator();
        $i2 = $this->getIterator();

        $i1->shouldHaveType(LazyIterator::class);
        $i2->shouldHaveType(LazyIterator::class);

        $i1->shouldBeIndependentFrom($i2);
    }

    /*
     * TODO Map... Move to all classes.
     */

    function it_should_produce_option_for_existing_key_search()
    {
        $tuples = static::getDefaultSourceTuples();

        $this->beConstructedFromTuples($tuples);

        $expectedTuple = $tuples[0];

        $this->findByKey($expectedTuple[0])->shouldBeLike(new Some($expectedTuple[1]));
    }

    function it_should_produce_option_for_not_existing_key_search()
    {
        $this->beConstructedWith($this->getDefaultSource());

        $this->findByKey(new DateTime('2015-01-01 00:00:00'))->shouldBe(None::create());
    }

    function it_should_produce_value_for_existing_key_request()
    {
        $tuples = static::getDefaultSourceTuples();

        $this->beConstructedFromTuples($tuples);

        $expectedTuple = $tuples[0];

        $this->get($expectedTuple[0])->shouldBe($expectedTuple[1]);
    }

    function it_should_throw_exception_for_not_existing_key_request()
    {
        $this->beConstructedWith($this->getDefaultSource());

        $this->shouldThrow(Exception::class)->duringGet(new DateTime('2015-01-01 00:00:00'));
    }
}
