<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PairMapKeys extends PairParts
{
    public function __construct($pairs)
    {
        parent::__construct($pairs, static::PART_KEY);
    }

    public function contains($key)
    {
        return $this->pairs->containsKey($key);
    }
}
