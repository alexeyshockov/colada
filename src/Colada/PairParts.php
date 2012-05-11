<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
abstract class PairParts extends CollectionMapIterator implements \Countable
{
    const PART_KEY     = 0;
    const PART_ELEMENT = 1;

    protected $pairs;

    public function __construct(Pairs $pairs, $part)
    {
        if (!in_array($part, array(static::PART_KEY, static::PART_ELEMENT))) {
            throw new \InvalidArgumentException('Unknown part.');
        }

        $this->pairs = $pairs;

        parent::__construct(
            $this->pairs,
            function($pair) use($part) {
                return $pair[$part];
            }
        );
    }

    public function count()
    {
        return count($this->pairs);
    }
}
