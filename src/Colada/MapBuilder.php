<?php

namespace Colada;

/**
 * Builder for constructing immutable maps.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class MapBuilder
{
    /**
     * @var \ArrayIterator|\SplObjectStorage
     */
    protected $map;

    public function __construct()
    {
        $this->map = new \ArrayIterator(array());
    }

    public function __clone()
    {
        $this->map = clone $this->map;
    }

    /**
     * @param mixed $key
     * @param mixed $element
     *
     * @return MapBuilder
     */
    public function put($key, $element)
    {
        if ($this->map instanceof \SplObjectStorage) {
            $key = $this->getObjectKey($key);
        } else {
            if (!is_scalar($key)) {
                // Transform to universal storage...
                $array   = $this->map;
                $storage = new \SplObjectStorage();
                foreach ($array as $key => $mapElement) {
                    $storage[$this->getObjectKey($key)] = $mapElement;
                }

                $this->map = $storage;

                $key = $this->getObjectKey($key);
            }
        }

        $this->map[$key] = $element;

        return $this;
    }

    /**
     * @param callback $mapper
     *
     * @return MapBuilder
     */
    public function mapElements($mapper)
    {
        Contracts::ensureCallable($mapper);

        if ($this->map instanceof \SplObjectStorage) {
            foreach ($this->map as $key) {
                $this->map[$key] = call_user_func($mapper, $this->map[$key]);
            }
        } else {
            foreach ($this->map as $key => $element) {
                $this->map[$key] = call_user_func($mapper, $element);
            }
        }

        return $this;
    }

    // TODO Duplicate from SplObjectStoragePairs. Remove.
    protected function getObjectKey($key)
    {
        return ((!is_object($key) || ($key instanceof NotObjectKey)) ? new NotObjectKey($key) : $key);
    }

    /**
     * @return Map
     */
    public function build()
    {
        if ($this->map instanceof \SplObjectStorage) {
            $pairs = new SplObjectStoragePairs($this->map);
        } else {
            $pairs = new ArrayIteratorPairs($this->map);
        }

        return new PairMap($pairs);
    }
}
