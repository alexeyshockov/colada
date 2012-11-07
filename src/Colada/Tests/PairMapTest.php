<?php

namespace Colada\Tests;

require_once "PHPUnit/Framework/Assert/Functions.php";

use Colada\PairMap,
    Colada\SplObjectStoragePairs,
    Colada\ArrayIteratorPairs;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PairMapTest extends \PHPUnit_Framework_TestCase
{
    private $dayNames;

    private $dayDates;

    public function setUp()
    {
        $this->dayDates = array(
            'today'     => ($today = new \DateTime()),
            'yesterday' => date_modify(clone $today, '-1 day'),
            'tomorrow'  => date_modify(clone $today, '+1 day'),
        );

        $storage = new \SplObjectStorage();

        $storage[$this->dayDates['today']]     = 'today';
        $storage[$this->dayDates['yesterday']] = 'yesterday';
        $storage[$this->dayDates['tomorrow']]  = 'tomorrow';

        // Assume we have map with 3 pairs.
        $this->dayNames = new PairMap(new SplObjectStoragePairs($storage));
    }

    /**
     * @test
     */
    public function richMapShouldBeSerializedToJsonAsPairs()
    {
        if (!(version_compare(PHP_VERSION, '5.4.0') >= 0)) {
            $this->markTestSkipped('Actual only for PHP 5.4.');
        }

        // Assume we have map with 3 pairs.
        $map = new PairMap(new ArrayIteratorPairs(new \ArrayIterator($array = array('one' => 1))));

        assertSame(json_encode($array), json_encode($map));
    }

    /**
     * @test
     */
    public function sizeShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        assertSame(3, count($map));
    }

    /**
     * @test
     */
    public function pairsSetSizeShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        assertSame(3, count($map->asPairs()));
    }

    /**
     * @test
     */
    public function shouldBeAvailableAsPairsSet()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        assertEquals(
            array(
                array($this->dayDates['today'],     'today'),
                array($this->dayDates['yesterday'], 'yesterday'),
                array($this->dayDates['tomorrow'],  'tomorrow'),
            ),
            $map->asPairs()->toArray()
        );
    }

    /**
     * @depends shouldBeAvailableAsPairsSet
     *
     * @test
     */
    public function filtrationShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $map = $map->acceptBy(function($key, $element) {
            return ($element != 'yesterday');
        });

        assertEquals(
            array(
                array($this->dayDates['today'],    'today'),
                array($this->dayDates['tomorrow'], 'tomorrow'),
            ),
            $map->asPairs()->toArray()
        );
    }

    /**
     * @depends shouldBeAvailableAsPairsSet
     *
     * @test
     */
    public function mappingShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $map = $map->mapBy(function($key, $element) {
            return array($key, strlen($element));
        });

        assertEquals(
            array(
                array($this->dayDates['today'],     5),
                array($this->dayDates['yesterday'], 9),
                array($this->dayDates['tomorrow'],  8),
            ),
            $map->asPairs()->toArray()
        );
    }

    /**
     * @depends shouldBeAvailableAsPairsSet
     *
     * @test
     */
    public function flatMappingShouldBeCorrectWithEqualsKeysInResult()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $map = $map->flatMapBy(function($key, $element) {
            return array(array('some_key', $key), array('some_element', $element));
        });

        // All keys will be merged.
        assertCount(2, $map);
        assertTrue($map->containsKey('some_key'));
        assertTrue($map->containsKey('some_element'));
    }

    /**
     * @depends shouldBeAvailableAsPairsSet
     *
     * @test
     */
    public function pickingShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $map = $map->pick(array($this->dayDates['today'], $this->dayDates['yesterday']));

        // Same keys will be merged.
        assertEquals(
            array(
                array($this->dayDates['today'],     'today'),
                array($this->dayDates['yesterday'], 'yesterday'),
            ),
            $map->asPairs()->toArray()
        );
    }

    /**
     * @depends shouldBeAvailableAsPairsSet
     *
     * @test
     */
    public function flippingShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $map = $map->flip();

        // Same keys will be merged.
        assertEquals(
            array(
                array('today',     $this->dayDates['today']),
                array('yesterday', $this->dayDates['yesterday']),
                array('tomorrow',  $this->dayDates['tomorrow']),
            ),
            $map->asPairs()->toArray()
        );
    }

    /**
     * @test
     */
    public function methodIsEmptyShouldBeCorrect()
    {
        $map = new PairMap(new ArrayIteratorPairs(new \ArrayIterator(array())));

        assertSame(true, $map->isEmpty());
    }
}
