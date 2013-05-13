<?php

namespace Colada;

use Colada\Helpers\CollectionHelper;

/**
 * Universal map implementation.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PairMap implements \Countable, \IteratorAggregate, Map
{
    /**
     * @var \Colada\Pairs
     */
    protected $pairs;

    /**
     * @var \Colada\Collection
     */
    protected $pairSet;

    /**
     * @var \Colada\PairMapKeySet
     */
    protected $keySet;

    /**
     * @var \Colada\Collection
     */
    protected $elements;

    /**
     * @param Pairs $pairs
     */
    public function __construct(Pairs $pairs)
    {
        $this->pairs    = $pairs;
        $this->pairSet  = new IteratorCollection($pairs);
        $this->keySet   = new PairMapKeySet(new PairMapKeys($pairs));
        $this->elements = new IteratorCollection(new PairMapElements($pairs));
    }

    /**
     * @param bool $static
     *
     * @return \Colada\MapBuilder
     */
    protected static function createMapBuilder($static = true)
    {
        $class = get_called_class();
        if (!$static) {
            $class = get_class();
        }

        return new MapBuilder($class);
    }

    /**
     * Mostly for standard PHP's foreach.
     *
     * @see asPairs()
     *
     * @throws \DomainException If map keys isn't scalars (PHP restrictions).
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        if (!($this->pairs instanceof Arrayable)) {
            throw new \DomainException('Only pairs with scalar keys are supported for standard PHP iterator.');
        }

        // Optimization for ArrayIteratorPairs.
        if ($this->pairs instanceof ArrayIteratorPairs) {
            return new IteratorProxy($this->pairs->getArrayIterator());
        } else {
            return new \ArrayIterator($this->pairs->toArray());
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->pairs);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (count($this) == 0);
    }

    /**
     * {@inheritDoc}
     */
    public function flip()
    {
        return $this->asPairs()
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[1], $pair[0]); },
                static::createMapBuilder(false)
            )->build();
    }

    /**
     * {@inheritDoc}
     */
    public function asElements()
    {
        return $this->elements;
    }

    /**
     * {@inheritDoc}
     */
    public function asKeys()
    {
        return $this->keySet;
    }

    /**
     * {@inheritDoc}
     */
    public function acceptBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this->asPairs()
            ->acceptBy($this->getCollectionCallbackFor($filter))
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                static::createMapBuilder()
            )->build();
    }

    /**
     * {@inheritDoc}
     */
    public function rejectBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this->acceptBy(CallbackHelper::invert($filter));
    }

    /**
     * @param callback $callback
     *
     * @return callback
     */
    protected function getCollectionCallbackFor($callback)
    {
        return function($pair) use($callback) {
            return call_user_func($callback, $pair[0], $pair[1]);
        };
    }

    /**
     * {@inheritDoc}
     */
    public function mapElementsBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return $this->asPairs()
            ->mapBy(function($pair) use($mapper) { return array($pair[0], call_user_func($mapper, $pair[1])); })
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                static::createMapBuilder(false)
            )->build();
    }

    /**
     * {@inheritDoc}
     */
    public function eachBy($processor)
    {
        Contracts::ensureCallable($processor);

        $this->asPairs()->eachBy($this->getCollectionCallbackFor($processor));

        return $this;
    }

    private function mapPairsBy($mapper, $mapType = 'mapBy')
    {
        return $this->asPairs()
            ->{$mapType}($this->getCollectionCallbackFor($mapper))
            ->foldBy(
                function($builder, $pair) {
                    if ($builder instanceof CollectionBuilder) {
                        return $builder->add($pair);
                    }

                    // Is pair like?
                    if (is_array($pair) && (count($pair) == 2)) {
                        return $builder->put($pair[0], $pair[1]);
                    } else {
                        $collectionBuilder = new CollectionBuilder();

                        // Downgrade to collection...
                        return $collectionBuilder
                            ->addAll($builder->build()->asPairs())
                            ->add($pair);
                    }
                },
                static::createMapBuilder(false)
            )->build();
    }

    /**
     * {@inheritDoc}
     */
    public function pick($keys)
    {
        return $this->acceptBy(function($key) use ($keys) {
            return CollectionHelper::in($key, $keys);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function mapBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return $this->mapPairsBy($mapper);
    }

    /**
     * {@inheritDoc}
     */
    public function flatMapBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return $this->mapPairsBy($mapper, 'flatMapBy');
    }

    /**
     * {@inheritDoc}
     */
    public function asPairs()
    {
        return $this->pairSet;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->pairs->getElementByKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function apply($key)
    {
        return $this->get($key)->orElse(function() { throw new \OutOfBoundsException('Key not found.'); });
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        if ($this->pairs instanceof Arrayable) {
            return $this->pairs->toArray();
        }

        throw new \DomainException('toArray() doesn\'t supported for this map object.');
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->pairs instanceof Arrayable) {
            return $this->pairs->toArray();
        } else {
            return $this->asPairs()->toArray();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return $this->asElements()->contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return $this->pairs->containsKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function put($key, $element)
    {
        $builder = static::createMapBuilder();

        // TODO MapBuilder::putAll().
        foreach ($this->asPairs() as $pair) {
            $builder->put($pair[0], $pair[1]);
        }

        // And last needed element...
        return $builder->put($key, $element)->build();
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        return $this->rejectBy(x()->at(0)->isEqualTo($key));
    }

    /**
     * @see \Colada\Map::containsKey()
     *
     * @param mixed $key
     *
     * @return boolean
     */
    public function offsetExists($key)
    {
        return $this->containsKey($key);
    }

    /**
     * @see \Colada\Map::get()
     *
     * @param mixed $key
     *
     * @return Option
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Not useful without ability to return new map (PHP restrictions).
     *
     * @throws \DomainException In all cases, this map are immutable.
     *
     * @param mixed $key
     * @param mixed $element
     *
     * @return void
     */
    public function offsetSet($key, $element)
    {
        throw new \DomainException('Unable to modify immutable collection.');
    }

    /**
     * Not useful without ability to return new map (PHP restrictions).
     *
     * @throws \DomainException In all cases, this map are immutable.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \DomainException('Unable to modify immutable collection.');
    }

    /**
     * {@inheritDoc}
     */
    public function __clone()
    {
        $builder = static::createMapBuilder();

        // TODO MapBuilder::putAll().
        foreach ($this->asPairs() as $pair) {
            $builder->put($pair[0], $pair[1]);
        }

        $map = $builder->build();

        // “pairs” field must be present in all child classes. If not, this method must be overridden.
        $this->__construct($map->pairs);
    }
}
