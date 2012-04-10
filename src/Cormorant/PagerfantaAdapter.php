<?php

namespace Cormorant;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class PagerfantaAdapter implements \Pagerfanta\Adapter\AdapterInterface
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return count($this->collection);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->collection->slice($offset, $length);
    }
}
