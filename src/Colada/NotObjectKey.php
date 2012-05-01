<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class NotObjectKey implements Equalable
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function isEqualTo($key)
    {
        return (is_object($key) && ($key instanceof static) && ($this->key == $key->key));
    }
}
