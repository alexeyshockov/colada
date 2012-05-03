<?php

namespace Colada\Tests;

/**
 * @todo More tests for \ArrayAccess.
 * @todo Tests for __get() and __set().
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class XTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \Colada\X::registerFunction();
    }

    /**
     * @test
     */
    public function shouldBeChainable()
    {
        $x = \x()->startsWith('Test')->isFalse();

        $this->assertFalse($x('Test string.'));
    }

    /**
     * @test
     */
    public function shouldPreserveOriginalMethods()
    {
        $x = \x()->getTimezone()->isNull();

        $this->assertFalse($x(new \DateTime()));
    }

    /**
     * @depends shouldBeChainable
     *
     * @test
     */
    public function shouldProvideNativeArrayGet()
    {
        $x = \x()[1]->isNull();

        $this->assertFalse($x(array(1, 2, 3)));
    }
}
