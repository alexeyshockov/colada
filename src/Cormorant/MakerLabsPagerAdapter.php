<?php

namespace Cormorant;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class MakerLabsPagerAdapter implements \MakerLabs\PagerBundle\Adapter\PagerAdapterInterface
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function getTotalResults()
    {
        return count($this->collection);
    }

    public function getResults($offset, $length)
    {
        return $this->collection->slice($offset, $length);
    }
}
