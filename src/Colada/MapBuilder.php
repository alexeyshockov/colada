<?php

namespace Colada;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class MapBuilder
{
    protected $map;

    public function __construct()
    {
        $this->map = new \SplObjectStorage();
    }

    public function __clone()
    {
        $this->map = clone $this->map;
    }

    public function put($key, $element)
    {
        $this->map[$this->getObjectKey($key)] = $element;

        return $this;
    }

    /**
     * @param callable $mapper
     *
     * @return MapBuilder
     */
    public function mapElements(callable $mapper)
    {
        foreach ($this->map as $key) {
            $this->map[$key] = $mapper($this->map[$key]);
        }

        return $this;
    }

    // TODO Duplicate from SplObjectStorageMap. Remove.
    protected function getObjectKey($key)
    {
        return ((!is_object($key) || ($key instanceof NotObjectKey)) ? new NotObjectKey($key) : $key);
    }

    public function build()
    {
        return new SplObjectStorageMap($this->map);
    }
}
