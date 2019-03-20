<?php

namespace Colada\Tests;

use PHPUnit\Framework\TestCase;

class IterTest extends TestCase
{
    /** @test */
    function should_sort_by_values()
    {
        $iter = [1, 4, 5, 2, 3];

        $sorted = \Colada\iter\uasort(function ($v1, $v2) { return $v2 <=> $v1; }, $iter);

        assertSame([5, 4, 3, 2, 1], array_values(iterator_to_array($sorted)));
    }

    /** @test */
    function should_maintain_keys()
    {
        $iter = [1, 4, 5, 2, 3];

        $sorted = \Colada\iter\uasort(function ($v1, $v2) { return $v2 <=> $v1; }, $iter);

        assertSame([2 => 5, 1 => 4, 4 => 3, 3 => 2, 0 => 1], iterator_to_array($sorted));
    }
}
