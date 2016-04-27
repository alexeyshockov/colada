<?php

namespace Colada;

trait MonadLike
{
    /**
     * @param mixed $v
     *
     * @return static
     */
    public static function unit($v)
    {
        if ($v instanceof static) {
            return $v;
        }

        return new static($v);
    }
}
