<?php

namespace Colada\X;

/**
 * @todo __isset()
 * @todo __unset()
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class FutureValue implements \ArrayAccess
{
    /**
     * @internal
     *
     * @var \Clojure
     */
    private $mapper;

    public function __construct()
    {
        $this->mapper = function($value) { return x($value); };
    }

    public function offsetGet($key)
    {
        return $this->__call(__FUNCTION__, [$key]);
    }

    public function offsetUnset($key)
    {
        return $this->__call(__FUNCTION__, [$key]);
    }

    public function offsetExists($key)
    {
        return $this->__call(__FUNCTION__, [$key]);
    }

    public function offsetSet($key, $value)
    {
        return $this->__call(__FUNCTION__, [$key, $value]);
    }

    public function __set($property, $value)
    {
        $mapper = $this->mapper;

        $this->mapper = function($value) use($mapper, $property, $value) {
            $mapper($value)->$property = $value;

            return $value;
        };

        return $this;
    }

    public function __get($property)
    {
        $mapper = $this->mapper;

        $this->mapper = function($value) use($mapper, $property) {
            return $mapper($value)->$property;
        };

        return $this;
    }

    public function __call($method, $arguments)
    {
        $mapper = $this->mapper;

        $this->mapper = function($value) use($mapper, $method, $arguments) {
            return call_user_func_array(array($mapper($value), $method), $arguments);
        };

        return $this;
    }

    public function __invoke($value)
    {
        $mapper = $this->mapper;

        // In the end return original value.
        return $mapper($value)->__getValue();
    }
}
