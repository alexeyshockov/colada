<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class SplObjectStoragePairs extends CollectionMapIterator implements Pairs
{
    /**
     * @var \SplObjectStorage
     */
    private $map;

    public function __construct(\SplObjectStorage $map)
    {
        $this->map = $map;

        $keyFinder = function($key) { return (($key instanceof NotObjectKey) ? $key->key : $key); };

        parent::__construct(
            $this->map,
            function($key) use ($keyFinder, $map) {
                return array($keyFinder($key), $map[$key]);
            }
        );
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->map);
    }

    /**
     * @param mixed $key
     *
     * @return Option
     */
    protected function getMapKey($key)
    {
        $key = $this->getObjectKey($key);

        if ($this->map->contains($key)) {
            return new Some($key);
        }

        // Search by equalable...
        foreach ($this->map as $mapKey) {
            if (ComparisonHelper::isEquals($key, $mapKey)) {
                return new Some($mapKey);
            }
        }

        return new None();
    }

    protected function getObjectKey($key)
    {
        return ((!is_object($key) || ($key instanceof NotObjectKey)) ? new NotObjectKey($key) : $key);
    }

    /**
     * {@inheritDoc}
     */
    public function getElementByKey($key)
    {
        return $this->getMapKey($key)->mapBy(function($key) { return $this->map[$key]; });
    }

    /**
     * {@inheritDoc}
     *
     * @todo Use in keys set.
     */
    public function containsKey($key)
    {
        return ($this->getMapKey($key) instanceof Some);
    }
}
