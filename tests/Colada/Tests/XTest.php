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
        \Colada\Colada::registerFunctions();
    }

    /**
     * @test
     */
    public function shouldBeChainable()
    {
        $x = x()->startsWith('Test')->isFalse();

        $this->assertFalse($x('Test string.'));

        $x = x()->content->isBlank();

        $object = new \stdClass();
        $object->content = '';

        $this->assertTrue($x($object));
    }

    /**
     * @test
     */
    public function shouldPreserveOriginalMethods()
    {
        $x = x()->getTimezone()->isNull();

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
