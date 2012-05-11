<?php

namespace Colada;

/**
 * General purpose collection implementation.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IteratorCollection
    implements Collection, \Countable, \IteratorAggregate
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @param \Iterator|null $collection
     */
    public function __construct(\Iterator $collection = null)
    {
        $this->iterator = $collection;
    }

    protected static function createCollectionBuilder($sizeHint = 0)
    {
        return new CollectionBuilder($sizeHint);
    }

    /**
     * @{inheritDoc}
     */
    public function isEmpty()
    {
        return ($this->count() > 0);
    }

    /**
     * @{inheritDoc}
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
     * @{inheritDoc}
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
     * @{inheritDoc}
     */
    public function isNoneMatchBy($matcher)
    {
        return !$this->isAnyMatchBy($matcher);
    }

    /**
     * @todo Binary search for sorted collections.
     *
     * @{inheritDoc}
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

    public function count()
    {
        if ($this->iterator instanceof \Countable) {
            return count($this->iterator);
        } else {
            return iterator_count($this->iterator);
        }
    }

    /**
     * @{inheritDoc}
     */
    public function slice($offset, $length)
    {
        Contracts::ensureInteger($offset, $length);
        Contracts::ensurePositiveNumber($offset, $length);

        return static::createCollectionBuilder($length)
            ->addAll(new \LimitIterator($this->iterator, $offset, $length))
            ->build();
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
     * @{inheritDoc}
     */
    public function pluck($key)
    {
        $builder = new CollectionBuilder(count($this));

        foreach ($this->iterator as $element) {
            if (is_array($element) || (is_object($element) && ($element instanceof \ArrayAccess))) {
                // \ArrayAccess allow any types for offsetExists() method, but core implementations throws warnings
                // for not scalar keys.
                if (@isset($element[$key])) {
                    $builder->add($element[$key]);
                }
            }

            if (is_object($element) && ($element instanceof Map)) {
                $element->get($key)->map(function($element) use ($builder) { $builder->add($element); });
            }
        }

        return $builder->build();
    }

    /**
     * @{inheritDoc}
     */
    public function eachBy($processor)
    {
        Contracts::ensureCallable($processor);

        foreach ($this as $element) {
            call_user_func($processor, $element);
        }
    }

    /**
     * @{inheritDoc}
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
     * Lazy.
     *
     * @param callback $filter
     *
     * @return Collection
     */
    public function acceptBy($filter)
    {
        Contracts::ensureCallable($filter);

        return new static(
            new CollectionFilterIterator(
                $this->iterator,
                $filter
            )
        );
    }

    /**
     * Lazy.
     *
     * @param callback $filter
     *
     * @return Collection
     */
    public function rejectBy($filter)
    {
        Contracts::ensureCallable($filter);

        return $this->acceptBy(function($element) use($filter) { return !call_user_func($filter, $element); });
    }

    /**
     * Lazy.
     *
     * @param callback|mixed $mapper
     *
     * @return Collection
     */
    public function mapBy($mapper)
    {
        if (!is_callable($mapper)) {
            $element = $mapper;

            $mapper = function() use($element) { return $element; };
        }

        return new static(
            new CollectionMapIterator(
                $this->iterator,
                $mapper
            )
        );
    }

    /**
     * Lazy.
     *
     * @see http://www.scala-lang.org/api/current/scala/collection/immutable/Set.html
     *
     * @param callback|\Traversable $mapper
     *
     * @return Collection
     */
    public function flatMapBy($mapper)
    {
        if (!is_callable($mapper)) {
            if (is_object($mapper) && ($mapper instanceof \Traversable)) {
                $elements = $mapper;

                $mapper = function() use($elements) { return $elements; };
            } else {
                throw new \InvalidArgumentException();
            }
        }

        return new static(
            new CollectionFlatMapIterator(
                $this->iterator,
                $mapper
            )
        );
    }

    /**
     * @param callback $folder
     * @param mixed    $accumulator
     *
     * @return mixed
     */
    public function foldBy($folder, $accumulator)
    {
        Contracts::ensureCallable($folder);

        // PHP don't support tail recursion optimization...
        foreach ($this->iterator as $element) {
            $accumulator = call_user_func($folder, $accumulator, $element);
        }

        return $accumulator;
    }

    /**
     * Good introduction to reduce: http://www.codecommit.com/blog/scala/scala-collections-for-the-easily-bored-part-2.
     *
     * @throws \Exception On empty collection.
     *
     * @param callback $reducer
     *
     * @return mixed
     */
    public function reduceBy($reducer)
    {
        Contracts::ensureCallable($reducer);

        if (count($this) == 0) {
            throw new \Exception('Unable reduce empty collection.');
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
     * @param callback $partitioner
     *
     * @return Collection[] Array with two elements. Suitable for PHP's list().
     */
    public function partitionBy($partitioner)
    {
        Contracts::ensureCallable($partitioner);

        $builder1 = static::createCollectionBuilder($this->iterator);
        $builder2 = static::createCollectionBuilder($this->iterator);
        foreach ($this->iterator as $element) {
            if (call_user_func($partitioner, $element)) {
                $builder1->add($element);
            } else {
                $builder2->add($element);
            }
        }

        return array($builder1->build(), $builder2->build());
    }

    /**
     * @param callback|null $unzipper
     *
     * @return Collection[] Array with two elements. Suitable for PHP's list().
     */
    public function unzip($unzipper = null)
    {
        Contracts::ensureCallable($unzipper);

        $builder1 = static::createCollectionBuilder($this->iterator);
        $builder2 = static::createCollectionBuilder($this->iterator);

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
     * Zips two collections into one.
     *
     * Example:
     * <code>
     * $collection1 = (new CollectionBuilder())->addAll(array("Alice", "Bob", "Joe"))->build();
     * $collection2 = (new CollectionBuilder())->addAll(array(1, 2, 3))->build();
     *
     * $pairs = $collection1->zip($collection2)->toArray();
     * // array(
     * //     array('Alice', 1),
     * //     array('Bob', 2),
     * //     array('Joe', 3),
     * // )
     * </code>
     *
     * P.S. Haskell's and Scala's zipWith() may be implemented in two steps: 1. zip(), 2. mapBy().
     *
     * @todo Lazy. With CollectionZipIterator.
     *
     * @param Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return Collection
     */
    public function zip($collection)
    {
        $collection1 = $this;
        $collection2 = $this->normalizeCollection($collection);

        $collection1Iterator = $collection1->iterator;
        $collection2Iterator = $collection2->iterator;

        $builder = static::createCollectionBuilder($collection2Iterator);

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
     * Constructs set with elements which are in <i>either</i> sets (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Union will be (1, 2, 3, 4, 5).
     *
     * @todo Lazy. May be with CollectionFlatMapIterator.
     *
     * @param Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return Collection
     */
    public function union($collection)
    {
        $collection1 = $this;
        $collection2 = $collection;

        $builder = new SetBuilder($collection1->iterator);

        return $builder->addAll($collection1)->addAll($collection2)->build();
    }

    /**
     * Constructs set with elements which are in <i>both</i> sets (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Intersection will be (3).
     *
     * @todo Optimize?..
     * @todo Rename to "intersection"?
     *
     * @param Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return Collection
     */
    public function intersect($collection)
    {
        $collection1 = $this;
        $collection2 = $this->normalizeCollection($collection);

        $builder = new SetBuilder();

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
     * Lazy. Constructs set with elements which lie <i>outside</i> of a current collection and within another
     * collection (suitable mostly for sets).
     *
     * For example, we have two sets: (1, 2, 3) and (3, 4, 5). Complement will be (4, 5).
     *
     * @param Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return Collection
     */
    public function complement($collection)
    {
        $collection1 = $this;
        $collection2 = $this->normalizeCollection($collection);

        return $collection2->flatMapBy(function($element) use($collection1) {
            if (!$collection1->containts($element)) {
                return array($element);
            }

            return array();
        });
    }

    /**
     * @param Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return boolean
     */
    public function isPartOf($collection)
    {
        return $this->isAllMatchBy(_()->in($this->normalizeCollection($collection)));
    }

    /**
     * @param Collection|\Iterator|\IteratorAggregate|mixed $collection
     *
     * @return Collection
     */
    protected function normalizeCollection($collection)
    {
        if (is_object($collection) && (($collection instanceof Collection) || ($collection instanceof \Traversable))) {
            if (!($collection instanceof Collection)) {
                if ($collection instanceof \IteratorAggregate) {
                    $collection = $collection->getIterator();
                }

                $collection = new IteratorCollection($collection);
            }
        } else {
            $builder = new CollectionBuilder();

            $collection = $builder->addAll($collection)->build();
        }

        return $collection;
    }

    /**
     * @param callback $comparator
     *
     * @return Collection
     */
    public function sortBy($comparator)
    {
        Contracts::ensureCallable($comparator);

        $heap = new CustomHeap($comparator);
        foreach ($this->iterator as $element) {
            $heap->insert($element);
        }

        return static::createCollectionBuilder($heap)->addAll($heap)->build();
    }

    /**
     * @param callback $keyFinder
     *
     * @return Map
     */
    public function groupBy($keyFinder)
    {
        Contracts::ensureCallable($keyFinder);

        return $this->foldBy(
            function($multimapBuilder, $element) use($keyFinder) {
                return $multimapBuilder->add(call_user_func($keyFinder, $element), $element);
            },
            new MultimapBuilder(static::createCollectionBuilder())
        )->build();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        // TODO Drop keys to numeric indexes...
        return iterator_to_array($this->iterator);
    }
}
