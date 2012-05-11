<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PairMapElements extends PairParts
{
    public function __construct($pairs)
    {
        parent::__construct($pairs, static::PART_ELEMENT);
    }
}
