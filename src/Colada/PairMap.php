<?php

namespace Colada;

use Colada\Helpers\CollectionHelper;

/**
 * Universal map implementation.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PairMap implements Map, \Countable
{
    /**
     * @var \Colada\Pairs
     */
    private $pairs;

    /**
     * @var \Colada\IteratorCollection
     */
    private $pairSet;

    /**
     * @var \Colada\PairMapKeySet
     */
    private $keySet;

    /**
     * @var \Colada\IteratorCollection
     */
    private $elements;

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
     * @throws \DomainException If map keys isn't scalars (PHP restrictions).
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        // Check and method itself may be more smart, but...
        if (!($this->pairs instanceof Arrayable)) {
            throw new \DomainException('');
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
        return (count($this) > 0);
    }

    /**
     * @todo Lazy.
     *
     * @return \Colada\Map
     */
    public function flip()
    {
        return $this->asPairs()
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[1], $pair[0]); },
                new MapBuilder()
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
     * @todo Lazy.
     *
     * {@inheritDoc}
     */
    public function acceptBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this->asPairs()
            ->acceptBy($this->getCollectionCallbackFor($filter))
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                new MapBuilder()
            )->build();
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
     * @todo Lazy.
     *
     * {@inheritDoc}
     */
    public function rejectBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this->asPairs()
            ->rejectBy($this->getCollectionCallbackFor($filter))
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                new MapBuilder()
            )->build();
    }

    /**
     * @todo Lazy.
     *
     * {@inheritDoc}
     */
    public function mapElementsBy($mapper)
    {
        Contracts::ensureCallable($mapper);

        return $this->asPairs()
            ->mapBy(function($pair) use($mapper) { return array($pair[0], call_user_func($mapper, $pair[1])); })
            ->foldBy(
                function($builder, $pair) { return $builder->put($pair[0], $pair[1]); },
                new MapBuilder()
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
                new MapBuilder()
            )->build();
    }

    /**
     * @todo Lazy.
     *
     * {@inheritDoc}
     */
    public function pick($keys)
    {
        $checker = function($key) use ($keys) { return CollectionHelper::in($key, $keys); };

        return $this->asPairs()
            ->foldBy(
                function($builder, $pair) use($checker) {
                    if ($checker($pair[0])) {
                        $builder->put($pair[0], $pair[1]);
                    }

                    return $builder;
                },
                new MapBuilder()
            )->build();
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
        return $this->get($key)->orElse(function() { throw new \InvalidArgumentException('Key not found.'); });
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        if ($this->pairs instanceof Arrayable) {
            return $this->pairs->toArray();
        }

        throw new \RuntimeException('toArray() doesn\'t supported for this map.');
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
     * @param mixed $key
     * @param mixed $element
     *
     * @return \Colada\Map
     */
    public function put($key, $element)
    {
        $builder = new MapBuilder();

        // TODO MapBuilder::putAll()?
        foreach ($this->asPairs() as $key => $element) {
            $builder->put($key, $element);
        }

        // And last needed element...
        return $builder->put($key, $element)->build();
    }

    /**
     * @param mixed $key
     *
     * @return \Colada\Map
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
     * Not useful without returning value...
     *
     * @param mixed $key
     * @param mixed $element
     *
     * @return void
     */
    public function offsetSet($key, $element)
    {
        // TODO Proper exception type.
        throw new \DomainException('Unable to modify immutable collection.');
    }

    /**
     * Not useful without returning value...
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        // TODO Proper exception type.
        throw new \DomainException('Unable to modify immutable collection.');
    }
}
