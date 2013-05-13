<?php

namespace Colada\Tests\Fixtures;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Employee extends Person
{
    protected $position;

    public function __construct($name, $position)
    {
        parent::__construct($name);

        $this->position = $position;
    }
}
