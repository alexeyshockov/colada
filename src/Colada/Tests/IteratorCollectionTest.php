<?php

namespace Colada\Tests;

require_once "PHPUnit/Framework/Assert/Functions.php";

use Colada\IteratorCollection;

use Colada\None;

/**
 * @todo partitionBy()
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IteratorCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Covers not only splitAt() method, but takeTo() and dropFrom() too.
     *
     * @test
     */
    public function splitShouldBeCorrectOnNotEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        list($collection1, $collection2) = $collection->splitAt(2);

        assertSame(2, count($collection1));
        assertTrue($collection1->contains(1));
        assertTrue($collection1->contains(2));

        assertSame(1, count($collection2));
        assertTrue($collection2->contains(3));
    }

    /**
     * @test
     */
    public function splitShouldBeCorrectOnEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array()));

        list($collection1, $collection2) = $collection->splitAt(2);

        assertSame(0, count($collection1));
        assertSame(0, count($collection2));
    }

    /**
     * @test
     */
    public function joinShouldBeCorrectForStringCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        assertSame('1, 2, 3', $collection->join(', '));
    }

    /**
     * @test
     */
    public function joinShouldNotWorkForNonStringCollection()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function headShouldBeCorrectOnNotEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        assertEquals(option(1), $collection->head());
    }

    /**
     * @test
     */
    public function headShouldBeCorrectOnEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array()));

        assertEquals(new None(), $collection->head());
    }

    /**
     * @test
     */
    public function lastElementShouldBeCorrectOnNotEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        assertEquals(option(3), $collection->last());
    }

    /**
     * @test
     */
    public function lastElementShouldBeCorrectOnEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array()));

        assertEquals(new None(), $collection->last());
    }

    /**
     * @test
     */
    public function tailShouldBeCorrectOnNotEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $tail = $collection->tail();

        assertSame(2, count($tail));
        assertTrue($tail->contains(2));
        assertTrue($tail->contains(3));
    }

    /**
     * @test
     *
     * @expectedException \UnderflowException
     */
    public function tailShouldBeCorrectOnEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array()));

        $tail = $collection->tail();
    }

    /**
     * @test
     */
    public function groupingShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $map = $collection->groupBy(function($element) {
            return ($element % 2);
        });

        assertTrue($map instanceof \Colada\Map);
        assertSame(2, count($map));
        assertSame(array(2), $map->apply(0)->toArray());
        assertSame(array(1, 3), $map->apply(1)->toArray());
    }

    /**
     * @test
     */
    public function flattenShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(array(1, 2), array(3, 4))));

        $flattenCollection = $collection->flatten();

        assertSame(4, count($flattenCollection));
        assertTrue($flattenCollection->contains(1));
        assertTrue($flattenCollection->contains(2));
        assertTrue($flattenCollection->contains(3));
        assertTrue($flattenCollection->contains(4));
    }

    /**
     * @test
     */
    public function filtrationShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $filteredCollection = $collection->acceptBy(function($element) {
            return ($element % 2);
        });

        assertSame(2, count($filteredCollection));
        assertTrue($filteredCollection->contains(1));
        assertTrue($filteredCollection->contains(3));
    }

    /**
     * @test
     */
    public function foldingShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $sum = $collection->foldBy(function($sum, $element) {
            return ($sum + $element);
        }, 2);

        assertSame(8, $sum);
    }

    /**
     * @test
     */
    public function mappingShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));

        $collection = $collection->mapBy(function($element) { return $element + 1; });

        assertSame(array(2, 3, 4), $collection->toArray());
    }

    /**
     * @test
     */
    public function flatMappingShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array('Some ', 'text.')));

        // Split by chars.
        $collection = $collection->flatMapBy(function($element) { return str_split($element); });

        assertSame(10, count($collection));
        assertSame(str_split('Some text.'), $collection->toArray());
    }

    /**
     * @test
     */
    public function slicingShouldBeCorrectForCorrectBounds()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 4, 5)));

        $collection = $collection->slice(0, 3);

        assertSame(3, count($collection));
        assertSame(array(1, 2, 3), $collection->toArray());
    }

    /**
     * @test
     */
    public function sortingShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 5, 4, 3, 2)));

        $collection = $collection->sortBy(function($element1, $element2) {
            return $element1 - $element2;
        });

        assertSame(array(1, 2, 3, 4, 5), $collection->toArray());
    }

    /**
     * @test
     */
    public function reduceShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1, 5, 4, 3, 2)));

        $string = $collection->reduceBy(function($string, $element) { return $string.' '.$element; });

        assertSame('1 5 4 3 2', $string);
    }

    /**
     * @test
     */
    // TODO Concrete exception class in reduceBy()...
    public function reduceShouldNotBeAvailableForEmptyCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array()));

        try {
            $collection->reduceBy(function($string, $element) { return $string.' '.$element; });

            $this->fail();
        } catch (\Exception $exception) {

        }
    }

    /**
     * @test
     */
    public function reduceShouldBeCorrectForCollectionWithOnlyOneElement()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array(1)));

        $string = $collection->reduceBy(function($string, $element) { return $string.' '.$element; });

        assertEquals('1', $string);
    }

    /**
     * @test
     */
    public function unionShouldBeCorrectForSets()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(3, 4, 5)));

        assertSame(array(1, 2, 3, 4, 5), $collection1->union($collection2)->toArray());
    }
    /**
     * Collection's elements may be not unique.
     *
     * @test
     */
    public function unionShouldBeCorrectForCollections()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 2, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(3, 3, 4, 5, 5)));

        assertSame(array(1, 2, 3, 4, 5), $collection1->union($collection2)->toArray());
    }

    /**
     * @test
     */
    public function intersectionShouldBeCorrectForSets()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(3, 4, 5)));

        assertSame(array(3), $collection1->intersect($collection2)->toArray());
    }

    /**
     * Collection's elements may be not unique.
     *
     * @test
     */
    public function intersectionShouldBeCorrectForCollections()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(3, 3, 4, 5, 5)));

        assertSame(array(3), $collection1->intersect($collection2)->toArray());
    }

    /**
     * @test
     */
    public function complementShouldBeCorrectForSets()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(3, 4, 5)));

        assertSame(array(4, 5), $collection1->complement($collection2)->toArray());
    }

    /**
     * Collection's elements may be not unique.
     *
     * @test
     */
    public function complementShouldBeCorrectForCollections()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(3, 3, 4, 5, 5)));

        assertSame(array(4, 5), $collection1->complement($collection2)->toArray());
    }

    /**
     * For collections and sets.
     *
     * @test
     */
    public function partCheckingShouldBeCorrect()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 3, 3, 4, 5, 5)));

        assertTrue($collection1->isPartOf($collection2));
    }

    /**
     * @test
     */
    public function zippingShouldBeCorrectForCollectionsOfSameSize()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 3)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array('one', 'two', 'three', 'three')));

        assertSame(
            array(
                array(1, 'one'),
                array(2, 'two'),
                array(3, 'three'),
                array(3, 'three'),
            ),
            $collection1->zip($collection2)->toArray()
        );
    }

    /**
     * @test
     */
    public function zippingShouldBeCorrectForCollectionsOfNotSameSize()
    {
        $collection1 = new IteratorCollection(new \ArrayIterator(array(1, 2, 3, 3, 4)));
        $collection2 = new IteratorCollection(new \ArrayIterator(array('one', 'two', 'three', 'three')));

        assertSame(
            array(
                array(1, 'one'),
                array(2, 'two'),
                array(3, 'three'),
                array(3, 'three'),
            ),
            $collection1->zip($collection2)->toArray()
        );
    }

    public function defaultUnzippingShouldBeCorrectForTupleCollection()
    {
        $collection = new IteratorCollection(new \ArrayIterator(
            array(
                array(1, 'one'),
                array(2, 'two'),
                array(3, 'three'),
                array(3, 'three'),
            )
        ));

        list($collection1, $collection2) = $collection->unzip();

        assertSame(array(1, 2, 3, 3), $collection1->toArray());
        assertSame(array('one', 'two', 'three', 'three'), $collection1->toArray());
    }

    /**
     * @test
     */
    public function defaultUnzippingShouldBeCorrectForNotTupleCollection()
    {
        $this->markTestIncomplete();

        // TODO Catch exception?
    }

    /**
     * Cloning in case of immutable collection "freeze" elements (useful for result of many lazy operations).
     *
     * @test
     */
    public function shouldBeClonable()
    {
        $collection = new IteratorCollection($array = new \ArrayIterator(array(1, 2, 3, 3)));

        $freezedCollection = clone $collection;

        assertTrue($freezedCollection !== $collection);
        assertSame($collection->toArray(), $freezedCollection->toArray());

        $array[4] = 5;

        assertSame(array(1, 2, 3, 3), $freezedCollection->toArray());
    }

    /**
     * @test
     */
    public function methodIsEmptyShouldBeCorrect()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array()));

        assertSame(true, $collection->isEmpty());
    }

    /**
     * @test
     */
    public function methodIfContainsShouldNotCallCallbackIfElementNotExists()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array('pink', 'blue', 'red', 'green')));

        $callback = function() {
            throw new \Exception();
        };

        $collection->ifContains('black', $callback)->add('black')->toArray();
    }

    /**
     * @test
     *
     * @expectedException \Exception
     */
    public function methodIfContainsShouldCallCallbackIfElementExists()
    {
        $collection = new IteratorCollection(new \ArrayIterator(array('pink', 'blue', 'red', 'green')));

        $callback = function() {
            throw new \Exception();
        };

        $collection->ifContains('pink', $callback)->add('pink')->toArray();
    }
}
