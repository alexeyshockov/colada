<?php

namespace Colada;

use Colada\Helpers\TypeHelper;

Colada::registerFunctions();

/**
 * General purpose collection implementation.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IteratorCollection
    implements \Countable, \IteratorAggregate, Collection
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @throws \InvalidArgumentException For \NoRewindIterator iterators.
     *
     * @param \Traversable $collection
     */
    public function __construct(\Traversable $collection = null)
    {
        if ($collection instanceof \IteratorAggregate) {
            $collection = $collection->getIterator();
        }

        // Traversable?
        if (!($collection instanceof \Iterator) && !($collection instanceof \IteratorAggregate)) {
            $collection = new \IteratorIterator($collection);
        }

        if ($collection instanceof \NoRewindIterator) {
            throw new \InvalidArgumentException('Only iterators with rewind() are supported by this implementation.');
        }

        $this->iterator = $collection;
    }

    /**
     * @param \Countable|int|mixed $sizeHint
     * @param string|null          $class
     *
     * @return \Colada\CollectionBuilder
     */
    protected static function createCollectionBuilder($sizeHint = 0, $class = null)
    {
        return new CollectionBuilder($sizeHint, ($class ?: static::getClass()));
    }

    /**
     * @param \Countable|int|mixed $sizeHint
     * @param string|null          $class
     *
     * @return \Colada\SetBuilder
     */
    protected static function createSetBuilder($sizeHint = 0, $class = null)
    {
        return new SetBuilder($sizeHint, ($class ?: static::getClass()));
    }

    /**
     * Override this method in general purpose child classes.
     *
     * @param bool $static
     *
     * @return string
     */
    protected static function getClass($static = true)
    {
        $class = get_class();
        if (!$static) {
            $class = get_called_class();
        }

        return $class;
    }

    /**
     * Small helper.
     *
     * @param string $class       Class name (mainly from getClass() method).
     * @param mixed  $traversable Constructor parameter.
     *
     * @return \Colada\Collection
     */
    protected static function createCollection($class, $traversable = null)
    {
        return new $class($traversable);
    }

    /**
     * @return \Colada\MapBuilder
     */
    protected static function createMapBuilder()
    {
        return new MapBuilder();
    }

    /**
     * @param \Colada\CollectionBuilder|null $collectionBuilder
     *
     * @return \Colada\MultimapBuilder
     */
    protected static function createMultimapBuilder($collectionBuilder = null)
    {
        return new MultimapBuilder($collectionBuilder ?: static::createCollectionBuilder());
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->count() == 0);
    }

    /**
     * {@inheritDoc}
     */
    public function isAnyMatchBy($matcher)
    {
        Contracts::ensureCallable($matcher);

        foreach ($this->iterator as $element) {
            if (call_user_func($matcher, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isAllMatchBy($matcher)
    {
        Contracts::ensureCallable($matcher);

        foreach ($this->iterator as $element) {
            if (!call_user_func($matcher, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isNoneMatchBy($matcher)
    {
        return !$this->isAnyMatchBy($matcher);
    }

    /**
     * {@inheritDoc}
     *
     * @todo Binary search for sorted collections.
     */
    public function contains($element)
    {
        foreach ($this->iterator as $collectionElement) {
            if (ComparisonHelper::isEquals($element, $collectionElement)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function count()
    {
        if ($this->iterator instanceof \Countable) {
            return count($this->iterator);
        } else {
            return iterator_count($this->iterator);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length)
    {
        Contracts::ensureInteger($offset, $length);
        Contracts::ensurePositiveNumber($offset, $length);

        return new static(new \LimitIterator($this->iterator, $offset, $length));
    }

    /**
     * {@inheritDoc}
     */
    public function splitAt($element)
    {
        return array($this->takeTo($element), $this->dropFrom($element));
    }

    /**
     * {@inheritDoc}
     */
    public function takeTo($element)
    {
        return new static(new TakeIterator($this->iterator, $element));
    }

    /**
     * {@inheritDoc}
     */
    public function dropFrom($element)
    {
        return new static(new DropIterator($this->iterator, $element));
    }

    /**
     * Should "track" original object (immutable in our case, but source iterator may be not).
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        // Ensure immutable.
        return new IteratorProxy($this->iterator);
    }

    /**
     * {@inheritDoc}
     */
    public function pluck($key)
    {
        $builder = static::createCollectionBuilder(count($this), static::getClass(false));

        foreach ($this->iterator as $element) {
            // array or \ArrayAccess.
            if (is_array($element) || (is_object($element) && ($element instanceof \ArrayAccess))) {
                // \ArrayAccess allow any types for offsetExists() method, but core implementations throws warnings
                // for not scalar keys.
                if (@isset($element[$key])) {
                    $builder->add($element[$key]);

                    continue;
                }
            }

            // \Colada\Map.
            if (is_object($element) && ($element instanceof Map)) {
                $element->get($key)->map(function($element) use ($builder) { $builder->add($element); });

                continue;
            }

            // Object. Getters or simple fields.
            if (is_object($element)) {
                $object = new \ReflectionObject($element);

                // Fields.
                if ($object->hasProperty($key) && $object->getProperty($key)->isPublic()) {
                    $builder->add($element->$key);

                    continue;
                }

                // Getters.
                $getter = 'get'.ucfirst($key);
                if ($object->hasMethod($getter) && $object->getMethod($getter)->isPublic()) {
                    $builder->add($element->$getter());

                    continue;
                }
            }
        }

        return $builder->build();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function eachBy($processor)
    {
        Contracts::ensureCallable($processor);

        foreach ($this as $element) {
            call_user_func($processor, $element);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function findBy($filter)
    {
        Contracts::ensureCallable($filter);

        foreach ($this->iterator as $element) {
            if (call_user_func($filter, $element)) {
                return new Some($element);
            }
        }

        return new None();
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function acceptBy($filter)
    {
        Contracts::ensureCallable($filter);

        return new static(new CollectionFilterIterator(
            $this->iterator,
            $filter
        ));
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function rejectBy($filter)
    {
        // $filter type will be checked inside acceptBy().
        return $this->acceptBy(CallbackHelper::invert($filter));
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function mapBy($mapper)
    {
        if (!is_callable($mapper)) {
            $element = $mapper;

            $mapper = function() use($element) { return $element; };
        }

        return static::createCollection(static::getClass(false), new CollectionMapIterator(
            $this->iterator,
            $mapper
        ));
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function replace($filter, $value)
    {
        return $this->replaceBy($filter, function() use($value) { return $value; });
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function replaceBy($filter, $value)
    {
        if (!is_callable($filter)) {
            $filterValue = $filter;
            $filter      = function($element) use ($filterValue) {
                return ComparisonHelper::isEquals($filterValue, $element);
            };
        }

        Contracts::ensureCallable($filter);
        Contracts::ensureCallable($value);

        return new static(new CollectionMapIterator(
            $this->iterator,
            function($element) use($filter, $value) {
                if (call_user_func($filter, $element)) {
                    return call_user_func($value, $element);
                } else {
                    return $element;
                }
            }
        ));
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function flatten()
    {
        return $this->flatMapBy(x());
    }

    /**
     * {@inheritDoc}
     */
    // Lazy.
    public function flatMapBy($mapper)
    {
        if (!is_callable($mapper)) {
            if (is_array($mapper) || TypeHelper::isTraversable($mapper)) {
                $elements = $mapper;

                $mapper = function() use($elements) { return $elements; };
            } else {
                throw new \InvalidArgumentException('Mapper must be callback, array or \Traversable.');
            }
        }

        return static::createCollection(static::getClass(false), new CollectionFlatMapIterator(
            $this->iterator,
            $mapper
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function foldBy($folder, $accumulator = null)
    {
        Contracts::ensureCallable($folder);

        // PHP don't support tail recursion optimization...
        foreach ($this->iterator as $element) {
            $accumulator = call_user_func($folder, $accumulator, $element);
        }

        return $accumulator;
    }

    /**
     * {@inheritDoc}
     */
    public function reduceBy($reducer)
    {
        Contracts::ensureCallable($reducer);

        if (count($this) == 0) {
            throw new \UnderflowException('Unable reduce empty collection.');
        }

        if (count($this) == 1) {
            foreach ($this->iterator as $element) {
                return $element;
            }
        }

        $this->iterator->rewind();

        $reduced = $this->iterator->current();
        $this->iterator->next();

        while ($this->iterator->valid()) {
            $element = $this->iterator->current();

            $reduced = call_user_func($reducer, $reduced, $element);

            $this->iterator->next();
        }

        return $reduced;
    }

    /**
     * {@inheritDoc}
     */
    public function partitionBy($partitioner)
    {
        // $partitioner type will be checked inside acceptBy().
        return array($this->acceptBy($partitioner), $this->rejectBy($partitioner));
    }

    /**
     * {@inheritDoc}
     */
    public function unzip($unzipper = null)
    {
        if ($unzipper) {
            Contracts::ensureCallable($unzipper);
        }

        $builder1 = static::createCollectionBuilder($this->iterator, static::getClass(false));
        $builder2 = static::createCollectionBuilder($this->iterator, static::getClass(false));

        foreach ($this->iterator as $element) {
            if ($unzipper) {
                $element = call_user_func($unzipper, $element);
            }

            $builder1->add($element[0]);
            $builder1->add($element[1]);
        }

        return array($builder1->build(), $builder2->build());
    }

    /**
     * {@inheritDoc}
     */
    // TODO Lazy. With CollectionZipIterator.
    public function zip($collection)
    {
        $collection1 = $this;
        $collection2 = $this->normalizeCollection($collection);

        $collection1Iterator = $collection1->iterator;
        $collection2Iterator = $collection2->iterator;

        $builder = static::createCollectionBuilder($collection2Iterator, static::getClass(false));

        $collection1Iterator->rewind();
        $collection2Iterator->rewind();

        while ($collection1Iterator->valid() && $collection2Iterator->valid()) {
            $builder->add(array($collection1Iterator->current(), $collection2Iterator->current()));

            $collection1Iterator->next();
            $collection2Iterator->next();
        }

        return $builder->build();
    }

    /**
     * {@inheritDoc}
     */
    public function union($collection)
    {
        $collection1 = $this;
        $collection2 = $collection;

        $builder = static::createSetBuilder($collection1->iterator);

        return $builder->addAll($collection1)->addAll($collection2)->build();
    }

    /**
     * {@inheritDoc}
     */
    public function intersect($collection)
    {
        $collection1 = $this;
        $collection2 = $this->normalizeCollection($collection);

        $builder = static::createSetBuilder();

        foreach ($collection1 as $element) {
            if ($collection2->contains($element)) {
                $builder->add($element);
            }
        }

        foreach ($collection2 as $element) {
            if ($collection1->contains($element)) {
                $builder->add($element);
            }
        }

        return $builder->build();
    }

    /**
     * {@inheritDoc}
     */
    public function complement($collection)
    {
        $collection1 = $this;
        $collection2 = $this->normalizeCollection($collection);

        $setBuilder = static::createSetBuilder();

        foreach ($collection2 as $element) {
            if (!$collection1->contains($element)) {
                $setBuilder->add($element);
            }
        }

        return $setBuilder->build();
    }

    /**
     * {@inheritDoc}
     */
    public function isPartOf($collection)
    {
        return $this->isAllMatchBy(x()->in($this->normalizeCollection($collection)));
    }

    /**
     * @param \Traversable|mixed $collection
     *
     * @return \Colada\Collection
     */
    protected function normalizeCollection($collection)
    {
        if (is_object($collection) && ($collection instanceof \Traversable)) {
            if (!($collection instanceof Collection)) {
                $collection = static::createCollection(static::getClass(false), $collection);
            }
        } else {
            $builder = static::createCollectionBuilder(0, static::getClass(false));

            $collection = $builder->addAll($collection)->build();
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function sortBy($comparator)
    {
        Contracts::ensureCallable($comparator);

        $heap = new CustomHeap($comparator);
        foreach ($this->iterator as $element) {
            $heap->insert($element);
        }

        // We cannot create collection from $heap, because it traversable only once.
        return static::createCollectionBuilder($heap)->addAll($heap)->build();
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return $this->head();
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        foreach ($this->iterator as $element) {
            $last = $element;
        }

        if (isset($last)) {
            return new Some($last);
        } else {
            return new None();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function head()
    {
        foreach ($this->iterator as $element) {
            return new Some($element);
        }

        return new None();
    }

    /**
     * {@inheritDoc}
     */
    public function tail()
    {
        if ($this->head() instanceof None) {
            // From PHP documentation: "Exception thrown when performing an invalid operation on an empty container,
            // such as removing an element".
            throw new \UnderflowException('Tail on empty collection is undefined.');
        }

        return new static(new TailIterator($this->iterator));
    }


    /**
     * {@inheritDoc}
     */
    public function groupBy($keyFinder, $uniqueKeys = false)
    {
        Contracts::ensureCallable($keyFinder);

        if (!$uniqueKeys) {
            return $this->foldBy(
                function($multimapBuilder, $element) use($keyFinder) {
                    return $multimapBuilder->add(call_user_func($keyFinder, $element), $element);
                },
                static::createMultimapBuilder()
            )->build();
        } else {
            return $this->foldBy(
                function($mapBuilder, $element) use($keyFinder) {
                    return $mapBuilder->put(call_user_func($keyFinder, $element), $element);
                },
                static::createMapBuilder()
            )->build();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add($element)
    {
        $builder = static::createCollectionBuilder($this);

        return $builder->addAll($this)->add($element)->build();
    }

    /**
     * {@inheritDoc}
     */
    public function remove($element)
    {
        return $this->reject($element);
    }

    /**
     * {@inheritDoc}
     */
    public function reject($element)
    {
        if (!$this->contains($element)) {
            return $this;
        } else {
            return $this->rejectBy(function($collectionElement) use ($element) {
                return ComparisonHelper::isEquals($collectionElement, $element);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function join($delimiter)
    {
        if (!is_scalar($delimiter)) {
            if (is_object($delimiter) && method_exists($delimiter, '__toString')) {
                $delimiter = (string) $delimiter;
            } else {
                throw new \InvalidArgumentException('Delimiter must be compatible with a string.');
            }
        }

        $delimiterValue = $delimiter;
        // TODO If elements are not compatible with a string?.. Throw exception.
        $delimiter      = function($string, $element) use($delimiterValue) {
            return $string.$delimiterValue.$element;
        };

        return $this->reduceBy($delimiter);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return iterator_to_array($this->iterator);
    }

    /**
     * {@inheritDoc}
     */
    public function toSet()
    {
        $builder = static::createSetBuilder($this);

        return $builder->addAll($this)->build();
    }

    /**
     * {@inheritDoc}
     */
    public function __clone()
    {
        // “iterator” field must be present in all child classes. If not, this method must be overridden.
        $collection = static::createCollectionBuilder(count($this))->addAll($this->iterator)->build();

        $this->__construct($collection->iterator);
    }
}
