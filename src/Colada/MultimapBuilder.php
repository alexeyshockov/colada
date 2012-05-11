<?php

namespace Colada;

/**
 * Builder for constructing immutable multimaps (Map<K, Collection<V>>).
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class MultimapBuilder extends MapBuilder
{
    /**
     * Prototype.
     *
     * @var CollectionBuilder
     */
    private $elementCollectionBuilder;

    /**
     * @param CollectionBuilder $elementCollectionBuilder Concrete collection builder (for example, collections or sets
     * for elements).
     */
    public function __construct(CollectionBuilder $elementCollectionBuilder)
    {
        parent::__construct();

        $this->elementCollectionBuilder = $elementCollectionBuilder;
    }

    /**
     * @param mixed $key
     * @param mixed $element
     *
     * @return MultimapBuilder
     */
    public function add($key, $element)
    {
        $this->apply($key, clone $this->elementCollectionBuilder)->add($element);

        return $this;
    }

    /**
     * Get key element or put with current and return it.
     *
     * @param mixed $key
     * @param mixed $element Element, if previously not exist.
     *
     * @return mixed Key element.
     */
    protected function apply($key, $element)
    {
        $map    = $this->map;
        $putter = array($this, 'put');

        return $this->getMapKey($key)
            ->mapBy(function($key) use($map) {
                return $map[$key];
            })
            ->orElse(function() use ($putter, $key, $element) {
                call_user_func($putter, $key, $element);

                return $element;
            });
    }

    protected function getMapKey($key)
    {
        if ($this->map instanceof \ArrayIterator) {
            if (!is_scalar($key)) {
                return new None();
            }

            if (isset($this->map[$key])) {
                return new Some($key);
            }

            return new None();
        }

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

    /**
     * @return Map
     */
    public function build()
    {
        $this->mapElements(function($collectionBuilder) {
            return $collectionBuilder->build();
        });

        return parent::build();
    }
}
