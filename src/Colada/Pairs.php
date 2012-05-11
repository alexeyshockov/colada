<?php

namespace Colada;

/**
 * "Backend" for default Map implementation, PairsMap.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface Pairs extends \Iterator, \Countable
{
    /**
     * @param mixed $key
     *
     * @return Option
     */
    public function getElementByKey($key);

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function containsKey($key);
}
