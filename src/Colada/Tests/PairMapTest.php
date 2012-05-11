<?php

namespace Colada\Tests;

use Colada\PairMap,
    Colada\SplObjectStoragePairs;

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
    public function sizeShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $this->assertSame(3, count($map));
    }

    /**
     * @test
     */
    public function pairsSetSizeShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $this->assertSame(3, count($map->asPairs()));
    }

    /**
     * @test
     */
    public function shouldBeAvailableAsPairsSet()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $this->assertEquals(
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

        $map = $map->acceptBy(function($pair) {
            return ($pair[1] != 'yesterday');
        });

        $this->assertEquals(
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

        $map = $map->mapBy(function($pair) {
            return array($pair[0], strlen($pair[1]));
        });

        $this->assertEquals(
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

        $map = $map->flatMapBy(function($pair) {
            return array($pair, $pair);
        });

        // Same keys will be merged.
        $this->assertEquals(
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
    public function pickingShouldBeCorrect()
    {
        // Assume we have map with 3 pairs.
        $map = $this->dayNames;

        $map = $map->pick(array($this->dayDates['today'], $this->dayDates['yesterday']));

        // Same keys will be merged.
        $this->assertEquals(
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
        $this->assertEquals(
            array(
                array('today',     $this->dayDates['today']),
                array('yesterday', $this->dayDates['yesterday']),
                array('tomorrow',  $this->dayDates['tomorrow']),
            ),
            $map->asPairs()->toArray()
        );
    }
}
