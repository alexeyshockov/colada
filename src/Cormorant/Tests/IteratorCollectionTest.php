<?php

namespace Cormorant\Tests;

use Cormorant\IteratorCollection;

/**
 * @todo partitionBy()
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IteratorCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeGroupable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $map = $collection->groupBy(function($element) {
            return ($element % 2);
        });

        $this->assertTrue($map instanceof \Cormorant\Map);
        $this->assertSame(2, count($map));
        $this->assertSame(array(2), $map->apply(0)->toArray());
        $this->assertSame(array(1, 3), $map->apply(1)->toArray());
    }

    /**
     * @test
     */
    public function shouldBeFilterable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $filteredCollection = $collection->filterBy(function($element) {
            return ($element % 2);
        });

        $this->assertSame(2, count($filteredCollection));
        $this->assertTrue($filteredCollection->contains(1));
        $this->assertTrue($filteredCollection->contains(3));
    }

    /**
     * @test
     */
    public function shouldBeFoldable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $sum = $collection->foldBy(function($sum, $element) {
            return ($sum + $element);
        }, 2);

        $this->assertSame(8, $sum);
    }

    /**
     * @test
     */
    public function shouldBeMappable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $collection = $collection->mapBy(function($element) { return $element + 1; });

        $this->assertSame(array(2, 3, 4), $collection->toArray());
    }

    /**
     * @test
     */
    public function shouldBeFlatMappable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array('Some text.')));

        // Split by chars.
        $collection = $collection->flatMapBy(function($element) { return str_split($element); });

        $this->assertSame(10, count($collection));
        $this->assertSame(str_split('Some text.'), $collection->toArray());
    }

    /**
     * @test
     */
    public function shouldBeSliceable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 4, 5)));

        $collection = $collection->slice(0, 3);

        $this->assertSame(3, count($collection));
        $this->assertSame(array(1, 2, 3), $collection->toArray());
    }

    /**
     * @test
     */
    public function shouldBeSortable()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 5, 4, 3, 2)));

        $collection = $collection->sortBy(function($element1, $element2) {
            return $element1 - $element2;
        });

        $this->assertSame(array(1, 2, 3, 4, 5), $collection->toArray());
    }
}
