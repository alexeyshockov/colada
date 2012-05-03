<?php

namespace Colada;

/**
 * Universal map implementation.
 *
 * P.S. foldBy(), eachBy() not implemented for simplify. KISS. All this methods available for pairs in $map->asPairs().
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class SplObjectStorageMap implements Map, \Countable
{
    /**
     * @var \SplObjectStorage
     */
    protected $map;

    /**
     * @var SplObjectStoragePairs
     */
    private $pairs;

    private $keys;

    private $elements;

    public function __construct(\SplObjectStorage $map)
    {
        $this->map = $map;

        $pairs = new SplObjectStoragePairs($this->map);

        $this->keys     = new IteratorCollection(new MapKeys($pairs));
        $this->elements = new IteratorCollection(new MapElements($pairs));
        $this->pairs    = new IteratorCollection($pairs);
    }

    public function isEmpty()
    {
        return (count($this) > 0);
    }

    public function count()
    {
        return count($this->map);
    }

    // TODO Lazy.
    public function flip()
    {
        return $this->asPairs()
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[1], $pair[0]); },
                new MapBuilder()
            )->build();
    }

    /**
     * @return Collection
     */
    public function asElements()
    {
        return $this->elements;
    }

    /**
     * Key set.
     *
     * @return Collection
     */
    public function asKeys()
    {
        return $this->keys;
    }

    // TODO Lazy.
    public function filterBy(callable $filter)
    {
        return $this->asPairs()
            ->filterBy($filter)
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                new MapBuilder()
            )->build();
    }

    // TODO Lazy.
    public function mapElementsBy(callable $mapper)
    {
        return $this->asPairs()
            ->mapBy(function($pair) use($mapper) { return [$pair[0], $mapper($pair[1])]; })
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                new MapBuilder()
            )->build();
    }

    private function mapPairsBy(callable $mapper, $mapType = 'mapBy')
    {
        return $this->asPairs()
            ->{$mapType}($mapper)
            ->foldBy(
                function($builder, $pair) {
                    if ($builder instanceof CollectionBuilder) {
                        return $builder->add($pair);
                    }

                    // Is pair?
                    if (is_array($pair) && (count($pair) == 2)) {
                        return $builder->put($pair[0], $pair[1]);
                    } else {
                        // Downgrade to collection...
                        return (new CollectionBuilder(count($this)))
                            ->addAll($builder->build()->asPairs())
                            ->add($pair);
                    }
                },
                new MapBuilder()
            )->build();
    }

    /**
     * Return a new Map, filtered to only have elements for the whitelisted keys.
     *
     * @param $keys
     *
     * @return Map
     */
    // TODO Lazy.
    public function pick($keys)
    {
        $checker = function($key) use ($keys) { return in_array($key, $keys); };

        $this->asPairs()
            ->foldBy(
                function($builder, $pair) use($checker) {
                    if ($checker($pair[0])) {
                        return $builder->put($pair[0], $pair[1]);
                    }
                },
                new MapBuilder()
            )->build();
    }

    /**
     * @param callable $mapper
     *
     * @return mixed Map or Collection.
     */
    // TODO Is this method lazy?
    public function mapBy(callable $mapper)
    {
        return $this->mapPairsBy($mapper);
    }

    /**
     * @param callable $mapper
     *
     * @return mixed Map or Collection.
     */
    public function flatMapBy(callable $mapper)
    {
        return $this->mapPairsBy($mapper, 'flatMapBy');
    }

    /**
     * @return Collection
     */
    public function asPairs()
    {
        return $this->pairs;
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

    protected function getOriginalKey($key)
    {
        return (($key instanceof NotObjectKey) ? $key->key : $key);
    }

    /**
     * @param mixed $key
     *
     * @return Option
     */
    public function get($key)
    {
        return $this->getMapKey($key)->mapBy(function($key) { return $this->map[$key]; });
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function apply($key)
    {
        // TODO Right exception.
        return $this->get($key)->orElse(function() { throw new \InvalidArgumentException(); });
    }

    public function contains($element)
    {
        return $this->asElements()->contains($element);
    }

    public function containsKey($key)
    {
        return ($this->getMapKey($key) instanceof Some);
    }
}
