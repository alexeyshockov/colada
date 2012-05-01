<?php

namespace Colada;

/**
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

    public function __construct(CollectionBuilder $elementCollectionBuilder)
    {
        parent::__construct();

        $this->elementCollectionBuilder = $elementCollectionBuilder;
    }

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
        return $this->getMapKey($key)->mapBy(function($key) {
            return $this->map[$key];
        })->orElse(function() use ($key, $element) {
            $this->put($key, $element);

            return $element;
        });
    }

    // TODO Duplicate from SplObjectStorageMap. Remove.
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

    public function build()
    {
        $this->mapElements(function($collectionBuilder) {
            return $collectionBuilder->build();
        });

        return parent::build();
    }
}
