<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface Equalable
{
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @param mixed $object
     *
     * @return bool
     */
    function isEqualTo($object);
}
