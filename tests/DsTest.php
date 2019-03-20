<?php

namespace Colada\Tests;

use function Colada\ds\group_by;
use PHPUnit\Framework\TestCase;

class DsTest extends TestCase
{
    /** @test */
    function should_group_elements_by_object_keys()
    {
        $iter = [1, 2, 3, 4, 5];

        $g1 = new \stdClass();
        $g2 = new \stdClass();

        $grouped = group_by(function ($val, $key) use ($g1, $g2) {
            return $key % 2 ? $g1 : $g2;
        }, $iter);

        assertTrue(isset($grouped[$g1]));
        assertTrue(isset($grouped[$g2]));

        assertSame([1 => 2, 3 => 4], $grouped[$g1]);
        assertSame([0 => 1, 2 => 3, 4 => 5], $grouped[$g2]);
    }
}
