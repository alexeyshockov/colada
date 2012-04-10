<?php

namespace Cormorant;

class MapKeys extends PairParts
{
    public function __construct($pairs)
    {
        parent::__construct($pairs, static::PART_KEY);
    }
}
