<?php

namespace Colada;

trait NumberCollectionLike
{
    use IterableLike;

    public function sum()
    {
        $result = 0;
        foreach ($this->internalIterator() as $v) {
            $result += $v;
        }

        return $result;
    }
}
