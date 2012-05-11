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
        \Colada\Colada::registerFunction();
    }

    /**
     * @test
     */
    public function shouldBeChainable()
    {
        $x = \Colada\_()->startsWith('Test')->isFalse();

        $this->assertFalse($x('Test string.'));
    }

    /**
     * @test
     */
    public function shouldPreserveOriginalMethods()
    {
        $x = \Colada\_()->getTimezone()->isNull();

        $this->assertFalse($x(new \DateTime()));
    }

    /**
     * Only for PHP 5.4.
     *
     * @depends shouldBeChainable
     */
    // TODO What shall we do for 5.3?
    public function shouldProvideNativeArrayGet()
    {

    }
}
