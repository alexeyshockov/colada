<?php

namespace Colada;

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
            ->acceptBy($filter)
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
    public function rejectBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this->asPairs()
            ->rejectBy($filter)
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

    private function mapPairsBy($mapper, $mapType = 'mapBy')
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
                        $builder = new CollectionBuilder(count($this));

                        // Downgrade to collection...
                        return $builder
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
        $checker = function($key) use ($keys) { return \Colada\Helpers\CollectionHelper::in($key, $keys); };

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
}
