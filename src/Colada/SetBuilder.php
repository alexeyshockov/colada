<?php

namespace Colada;

/**
 * Builder for constructing immutable sets.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class SetBuilder extends CollectionBuilder
{
    /**
     * Adds only unique elements.
     *
     * @param mixed $element
     *
     * @return SetBuilder
     */
    public function add($element)
    {
        foreach ($this->array as $collectionElement) {
            if (ComparisonHelper::isEquals($element, $collectionElement)) {
                // Duplicate.
                return $this;
            }
        }

        return parent::add($element);
    }

    /**
     * Adds only unique elements.
     *
     * @param array|\Traversable|mixed $elements
     *
     * @return SetBuilder
     */
    public function addAll($elements)
    {
        if (!(is_array($elements) || (is_object($elements) && ($elements instanceof \Traversable)))) {
            // Process not iterable values gracefully.
            $elements = array($elements);
        }

        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    protected function createCollection()
    {
        return new IteratorCollection($this->array);
    }
}
