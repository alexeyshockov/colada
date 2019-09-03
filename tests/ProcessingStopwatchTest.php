<?php

namespace Colada\Tests;

use Colada\ProcessingStopwatch;
use PHPUnit\Framework\TestCase;

class ProcessingStopwatchTest extends TestCase
{
    /** @test */
    function should_create_with_details()
    {
        $s = ProcessingStopwatch::create();

        assertInstanceOf(ProcessingStopwatch::class, $s);
    }
}
