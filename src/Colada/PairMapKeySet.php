<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PairMapKeySet extends IteratorCollection
{
    public function contains($key)
    {
        if ($this->iterator instanceof PairMapKeys) {
            return $this->iterator->contains($key);
        }

        return parent::contains($key);
    }
}
