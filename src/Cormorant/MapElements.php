<?php

namespace Cormorant;

class MapElements extends PairParts
{
    public function __construct($pairs)
    {
        parent::__construct($pairs, static::PART_ELEMENT);
    }
}
