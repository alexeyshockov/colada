<?php

namespace Colada\Tests\Fixtures;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Person
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isEqualTo(Person $person)
    {
        return $this->getName() == $person->getName();
    }
}
