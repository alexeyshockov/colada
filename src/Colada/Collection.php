<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
// TODO Implement Equalable?
interface Collection
{
    function contains($element);

    function isEmpty();

    /**
     * @return array
     */
    function toArray();
}
