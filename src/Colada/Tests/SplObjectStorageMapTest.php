<?php

namespace Colada\Tests;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class SplObjectStorageMapTest extends \PHPUnit_Framework_TestCase
{
    private $dayNames;

    private $dayDates;

    public function setUp()
    {
        $this->dayDates = [
            'today'     => new \DateTime(),
            'yesterday' => (new \DateTime())->modify('-1 day'),
            'tomorrow'  => (new \DateTime())->modify('+1 day'),
        ];

        $storage = new \SplObjectStorage();

        $storage[$this->dayDates['today']]     = 'today';
        $storage[$this->dayDates['yesterday']] = 'yesterday';
        $storage[$this->dayDates['tomorrow']]  = 'tomorrow';

        // Assume we have map with 3 pairs.
        $this->dayNames = new \Colada\SplObjectStorageMap($storage);
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
            [
                [$this->dayDates['today'],     'today'],
                [$this->dayDates['yesterday'], 'yesterday'],
                [$this->dayDates['tomorrow'],  'tomorrow'],
            ],
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

        $map = $map->filterBy(function($pair) {
            return ($pair[1] != 'yesterday');
        });

        $this->assertEquals(
            [
                [$this->dayDates['today'],    'today'],
                [$this->dayDates['tomorrow'], 'tomorrow'],
            ],
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
            return [$pair[0], strlen($pair[1])];
        });

        $this->assertEquals(
            [
                [$this->dayDates['today'],     5],
                [$this->dayDates['yesterday'], 9],
                [$this->dayDates['tomorrow'],  8],
            ],
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
            return [$pair, $pair];
        });

        // Same keys will be merged.
        $this->assertEquals(
            [
                [$this->dayDates['today'],     'today'],
                [$this->dayDates['yesterday'], 'yesterday'],
                [$this->dayDates['tomorrow'],  'tomorrow'],
            ],
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

        $map = $map->pick([$this->dayDates['today'], $this->dayDates['yesterday']]);

        // Same keys will be merged.
        $this->assertEquals(
            [
                [$this->dayDates['today'],     'today'],
                [$this->dayDates['yesterday'], 'yesterday'],
            ],
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
            [
                ['today',     $this->dayDates['today']],
                ['yesterday', $this->dayDates['yesterday']],
                ['tomorrow',  $this->dayDates['tomorrow']]
            ],
            $map->asPairs()->toArray()
        );
    }
}
