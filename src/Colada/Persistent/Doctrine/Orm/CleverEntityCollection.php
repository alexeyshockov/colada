<?php

namespace Colada\Persistent\Doctrine\Orm;

/**
 * With magic helpers.
 *
 * Candidate for trait...
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
abstract class CleverEntityCollection extends EntityCollection
{
    /**
     * @throws \InvalidArgumentException
     *
     * @param string $property
     * @param mixed $value
     *
     * @return EntityFilter
     */
    // TODO Proper exception for unknown property.
    abstract protected function getFilterForProperty($property, $value);

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $property
     * @param string $order
     *
     * @return EntityComparator
     */
    // TODO Proper exception for unknown property.
    abstract protected function getComparatorForProperty($property, $order);

    public function __call($method, $arguments)
    {
        $methodMap = array(
            'findBy'   => 'findByProperty',
            'filterBy' => 'filterByProperty',
            'sortBy'   => 'sortByProperty',
            'with'     => 'withAssociation',
        );

        foreach ($methodMap as $pattern => $destinationMethod) {
            if (\Colada\Helpers\StringHelper::startsWith($method, $pattern)) {
                $property = substr($method, strlen($pattern) - 1);

                array_unshift($arguments, $property);

                return call_user_func_array([$this, $destinationMethod], $arguments);
            }
        }
    }

    private function filterByProperty($property, $value)
    {
        $filter = $this->getFilterForProperty($property, $value);

        return $this->filterQueryBuilderBy(
            [$filter, 'updateQueryBuilder'],
            $filter
        );
    }

    private function findByProperty($property, $value)
    {
        $filter = $this->getFilterForProperty($property, $value);

        return $this->findInQueryBuilderBy(
            [$filter, 'updateQueryBuilder'],
            $filter
        );
    }

    private function sortByProperty($property, $order)
    {
        $comparator = $this->getComparatorForProperty($property, $order);

        return $this->sortQueryBuilderBy(
            [$comparator, 'updateQueryBuilder'],
            $comparator
        );
    }

    private function withProperty($property)
    {
        if ($this->queryBuilder) {
            $queryBuilder = clone $this->queryBuilder;

            $alias = $queryBuilder->getRootAliases();
            $alias = $alias[0];

            $queryBuilder->leftJoin($alias.$property, $property);

            // FIXME Add to select.

            return new static($queryBuilder, $this->isDetachingEnabled());
        }

        return $this;
    }
}
