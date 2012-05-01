<?php

namespace Colada\Persistent\Doctrine\Orm;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
abstract class EntityFilter
{
    protected $criteria = array();

    /**
     * @var \ReflectionClass
     */
    private $class;

    public static function fromArray($data)
    {
        $filter = new static();

        foreach ($data as $name => $value) {
            $methodName = 'set'.ucfirst($name).'Criteria';
            if (is_callable($filter, $methodName)) {
                $filter->{$methodName}($value);
            } else {
                // TODO Custom exception.
                throw new \InvalidArgumentException('Unknown criteria.');
            }
        }

        return filter;
    }

    protected function __construct()
    {
        $this->class = new \ReflectionClass($this->getEntityClass());
    }

    abstract protected function getEntityClass();

    public function __invoke($entity)
    {
        if (!is_object($entity) || !$this->class->isInstance($entity)) {
            return false;
        }

        $accepted = true;
        foreach (array_keys($this->criteria) as $criteria) {
            $accepted = $accepted && $this->{'check'.ucfirst($criteria).'Criteria'}($entity);

            // Optimization.
            if (!$accepted) {
                return $accepted;
            }
        }

        return $accepted;
    }

    public function updateQueryBuilder($queryBuilder, $alias)
    {
        foreach (array_keys($this->criteria) as $criteria) {
            $this->{'updateQbFor'.ucfirst($criteria).'Criteria'}($queryBuilder, $alias);
        }
    }
}
