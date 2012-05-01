<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
// Interface Pairs may be extracted (\Iterator + \Countable), but we are dynamic :)
class SplObjectStoragePairs extends CollectionMapIterator implements \Countable
{
    /**
     * @var \SplObjectStorage
     */
    private $map;

    public function __construct(\SplObjectStorage $map)
    {
        $this->map = $map;

        parent::__construct(
            $this->map,
            function($key) {
                return array($this->getOriginalKey($key), $this->map[$key]);
            }
        );
    }

    protected function getOriginalKey($key)
    {
        return (($key instanceof NotObjectKey) ? $key->key : $key);
    }

    public function count()
    {
        return count($this->map);
    }
}
