<?php

namespace Colada\Tests;

require_once "PHPUnit/Framework/Assert/Functions.php";

use Colada\ComparisonHelper;

use Colada\Tests\Fixtures\Bird;
use Colada\Tests\Fixtures\Bullfinch;
use Colada\Tests\Fixtures\Employee;

class ComparisonHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function equalsShouldBeCorrectForInheritedClass()
    {
        $element1 = new Employee('Alexey Shockov', 'Developer');
        $element2 = new Employee('Alexey Shockov', 'CTO');

        assertTrue(ComparisonHelper::isEquals($element1, $element2));
    }

    /**
     * @test
     */
    public function equalsShouldBeCorrectForInheritedClassWithoutEqualsMethod()
    {
        $element1 = new Bullfinch();
        $element2 = new Bird();

        assertFalse(ComparisonHelper::isEquals($element1, $element2));
    }
}
