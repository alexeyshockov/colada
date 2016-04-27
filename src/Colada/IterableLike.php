<?php

namespace Colada;

use LimitIterator;
use MultipleIterator;
use Traversable;
use Iterator;
use ArrayAccess;
use AppendIterator;
use SplDoublyLinkedList;
use SplFixedArray;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * @todo max()
 * @todo min()
 * @todo join() for strings
 * @todo partition() to return array? Will use useful with list()...
 * @todo replaceBy()
 *
 * removeDuplicates(), with custom comparator... No, we have toSet().
 * tail(), tails() — no.
 * init, inits() — no.
 */
trait IterableLike
{
    /**
     * @return Iterator
     */
    // TODO Rename somehow?..
    abstract protected function iterator();

    /**
     * It's useful to overwrite this method, when available.
     *
     * @return Iterator
     */
    // TODO Rename somehow?..
    protected function internalIterator()
    {
        return $this->iterator();
    }

    /**
     * @param callable $modifier
     *
     * @return static
     */
    protected function modify(callable $modifier)
    {
        return new static($modifier($this->internalIterator()));
    }

    /**
     * @return static
     */
    public function reversed()
    {
        return $this->modify(function ($iterator) {
            if ($iterator instanceof SplFixedArray) {
                $reversed = $this->reverseFixedArray($iterator);
            } else {
                $reversed = $this->reverseIterator($iterator);
            }

            return $reversed;
        });
    }

    /**
     * @param Traversable $iterator
     *
     * @return Iterator
     */
    protected function reverseIterator(Traversable $iterator)
    {
        // The same as SplStack here.
        $stack = new SplDoublyLinkedList();
        $stack->setIteratorMode(SplDoublyLinkedList:: IT_MODE_DELETE | SplDoublyLinkedList::IT_MODE_LIFO);

        foreach ($iterator as $k => $v) {
            $stack->push([$k, $v]);
        }

        // And produce reversed tuples...
        foreach ($stack as list($k, $v)) {
            yield $k => $v;
        }
    }

    /**
     * @param SplFixedArray $array
     *
     * @return Iterator
     */
    protected function reverseFixedArray(SplFixedArray $array)
    {
        $size = $array->getSize() - 1;
        for ($i = $size; $i >= 0; $i--) {
            yield $i => $array[$i];
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->first()->isEmpty();
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * @param mixed $initial
     * @param callable $folder
     *
     * @return mixed
     */
    public function foldLeft($initial, callable $folder)
    {
        $result = $initial;
        foreach ($this->internalIterator() as $k => $v) {
            $result = $folder($result, $v, $k);
        }

        return $result;
    }

    /**
     * @param callable $reducer
     *
     * @return Option
     */
    public function reduceLeft(callable $reducer)
    {
        $i = $this->internalIterator();

        // Like in foreach loop.
        $i->rewind();
        if ($i->valid()) {
            $result = $v = $i->current();
            $i->next();
            while ($i->valid()) {
                list($k, $v) = [$i->key(), $i->current()];

                $result = $reducer($result, $v, $k);
            }

            $result = new Some($result);
        } else {
            $result = None::create();
        }

        return $result;
    }

    /**
     * @return Option
     */
    public function first()
    {
        $first = None::create();
        foreach ($this->internalIterator() as $v) {
            $first = new Some($v);

            break;
        }

        return $first;
    }

    /**
     * @return Option
     */
    public function last()
    {
        $found = false;
        foreach ($this->internalIterator() as $v) {
            $found = true;
            // And just go to the end.
        }

        return $found ? new Some($v) : None::create();
    }

    /**
     * Keys will be preserved
     *
     * @param callable $p
     *
     * @return static
     */
    public function select(callable $p)
    {
        return $this->modify(function ($iterator) use ($p) {
            foreach ($iterator as $k => $v) {
                if ($p($v, $k)) {
                    yield $k => $v;
                }
            }
        });
    }

    /**
     * Keys will be preserved
     *
     * @param callable $p
     *
     * @return static
     */
    public function reject(callable $p)
    {
        return $this->select(not($p));
    }

    /**
     * Creates new collection with modified values (keys will be the same)
     *
     * @param callable $mapper
     *
     * @return static
     */
    public function map(callable $mapper)
    {
        return $this->modify(function ($iterator) use ($mapper) {
            foreach ($iterator as $k => $v) {
                yield $k => $mapper($v, $k);
            }
        });
    }

    /**
     * @param callable $action
     *
     * @return void
     */
    public function doForEach(callable $action)
    {
        foreach ($this->internalIterator() as $k => $v) {
            $action($v, $k);
        }
    }

    /**
     * @param callable $p
     *
     * @return bool
     */
    public function forAll(callable $p)
    {
        $result = true;
        foreach ($this->internalIterator() as $k => $v) {
            $result = $result && $p($v, $k);

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param callable $p
     *
     * @return bool
     */
    public function forSome(callable $p)
    {
        foreach ($this->internalIterator() as $k => $v) {
            if ($p($v, $k)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable $p
     *
     * @return bool
     */
    public function forNone(callable $p)
    {
        return $this->forAll(not($p));
    }

    /**
     * @return LazyIterator
     */
    public function flatten()
    {
        return LazyIterator::fromResult(function () {
            // Exactly this iterator, because we return lazy result.
            foreach ($this->iterator() as $values) {
                foreach ($values as $v) {
                    yield $v;
                }
            }
        });
    }

    /**
     * @param callable $mapper
     *
     * @return LazyIterator
     */
    public function flatMap(callable $mapper)
    {
        return $this->map($mapper)->flatten();
    }

    /**
     * @param callable $finder
     *
     * @return Option
     */
    public function find(callable $finder)
    {
        $found = None::create();
        foreach ($this->internalIterator() as $k => $v) {
            if ($finder($v, $k)) {
                $found = new Some($v);

                break;
            }
        }

        return $found;
    }

    /**
     * @param int $n
     *
     * @return static
     */
    public function drop($n)
    {
        return $this->dropWhile(function () use ($n) {
            static $i = 0;

            return $i++ < $n;
        });
    }

    /**
     * @param callable $filter
     *
     * @return static
     */
    public function dropWhile(callable $filter)
    {
        return $this->modify(function ($iterator) use ($filter) {
            foreach ($iterator as $k => $v) {
                if ($filter($v, $k)) {
                    // Skip element.
                    continue;
                }

                break;
            }

            // To prevent rewinding.
            return as_generator($iterator);
        });
    }

    /**
     * @param int $n
     *
     * @return static
     */
    public function take($n)
    {
        return $this->takeWhile(function () use ($n) {
            static $i = 0;

            return $i++ < $n;
        });
    }

    /**
     * @param callable $filter
     *
     * @return static
     */
    public function takeWhile(callable $filter)
    {
        return $this->modify(function ($iterator) use ($filter) {
            foreach ($iterator as $k => $v) {
                if ($filter($v, $k)) {
                    yield $k => $v;

                    // Go directly to the next element.
                    continue;
                }

                break;
            }
        });
    }

    /**
     * @param $value
     * @param bool|true $strict
     *
     * @return bool
     */
    public function contains($value, $strict = true)
    {
        $found = false;
        foreach ($this->internalIterator() as $v) {
            $found = ($v === $value || (!$strict && $v == $value));

            if ($found) {
                break;
            }
        }

        return $found;
    }

    /**
     * @param callable $predicate
     *
     * @return bool
     */
    public function exists(callable $predicate)
    {
        foreach ($this->internalIterator() as $k => $v) {
            if ($predicate($v, $k)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Only strict variant to match faster implementation
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function containsKey($key)
    {
        $found = false;
        $iterator = $this->internalIterator();
        if ($iterator instanceof ArrayAccess) {
            $found = $iterator->offsetExists($key);
        } else {
            foreach ($iterator as $k => $v) {
                $found = ($k === $key);

                if ($found) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Fills passed (by reference) variables with value, starting from the first value
     *
     * @param ...$vars
     */
    public function fill(&...$vars)
    {
        $i = $this->internalIterator();

        // Like in foreach loop.
        $i->rewind();
        foreach ($vars as &$var) {
            $var = $i->current();

            $i->next();
        }
    }

    /*
     * --- Hard? ---
     */

    /**
     * @param callable|null $comparator Without custom comparator tuples will be sorted by values.
     *
     * @return LazyIterator
     */
    // TODO Return static.
    public function sort(callable $comparator = null)
    {
        $comparator = $comparator ?: function ($t1, $t2) {
            $v1 = $t1[1];
            $v2 = $t2[1];

            return ($v1 > $v2 ? 1 : ($v1 < $v2 ? -1 : 0));
        };

        $heap = new CustomHeap($comparator);
        foreach ($this->internalIterator() as $k => $v) {
            $heap->insert([$k, $v]);
        }

        return LazyIterator::fromResult(function () use ($heap) {
            foreach ($heap as list($k, $v)) {
                yield $k => $v;
            }
        });
    }

    public function sortByKeys($comparator = null)
    {
        // TODO Implement. Use ArrayIterator sorts when possible.
    }

    public function sortByValues($comparator = null)
    {
        // TODO Implement. Use ArrayIterator sorts when possible.
    }

    /**
     * @param array|Traversable ...$sources
     *
     * @return LazyIterator
     */
    public function union(...$sources)
    {
        $sequenceIterator = new AppendIterator();
        foreach ($sources as $t) {
            $sequenceIterator->append(as_iterator($t));
        }

        return new LazyIterator($sequenceIterator);
    }

    /**
     * @param array|Traversable ...$sources
     *
     * @return static
     */
    public function intersect(...$sources)
    {
        // TODO Specific implementation for each type of collection.

        //
    }

    /**
     * Groups values using computed key
     *
     * Useful with {@see LazyIterator::groupToArrayMap()}/{@see LazyIterator::groupToHashMap()}.
     *
     * @param callable $indexer
     *
     * @return LazyIterator
     */
    public function reindex(callable $indexer)
    {
        return LazyIterator::fromResult(function () use ($indexer) {
            foreach ($this->iterator() as $k => $v) {
                $groupKey = $indexer($v, $k);

                yield $groupKey => $v;
            }
        });
    }

    /**
     * @param Traversable|array ...$sources
     *
     * @return LazyIterator
     */
    public function zip(...$sources)
    {
        // Normalize and validate.
        $sources = \array_map(as_iterator, $sources);

        array_unshift($sources, $this->iterator());

        $stack = new MultipleIterator(MultipleIterator::MIT_NEED_ALL | MultipleIterator::MIT_KEYS_NUMERIC);
        array_walk($sources, [$stack, 'attachIterator']);

        return new LazyIterator($stack);
    }

    /**
     * @param callable|null $unzipper
     * @param Builder[] ...$builders
     *
     * @return array Array of collections (useful with list() construct).
     */
    public function unzip(callable $unzipper = null, Builder ...$builders)
    {
        foreach ($this->internalIterator() as $k => $v) {
            $row = $unzipper($v, $k);

            // TODO Use ArrayMap builder by default, when $builders is empty.
            reset($builders);
            foreach ($row as $rk => $rv) {
                $builder = current($builders);
                if ($builder) {
                    $builder->add($rv, $rk);
                }

                next($builders);
            }
        }

        return \array_map(function ($b) { return $b->build(); }, $builders);
    }

    /**
     * @param int $offset
     * @param int|null $length By default all tuples after specified offset will be accepted
     *
     * @return LazyIterator
     */
    public function slice($offset = 0, $length = null)
    {
        return new LazyIterator(new LimitIterator($this->iterator(), $offset, $length));
    }

    /*
     * Pluck is complicated if we want to support many types (arrays, ArrayAccess, objects properties, objects
     * methods...). The same effect can be achieved with x():
     *
     * $collection->map(x()->getGroup())
     * $collection->flatMap(x()->getRoles())
     * $collection->map(x()['account'])
     * ...
     */
//    public function pluck($key) {}

    /*
     * --- Transformations ---
     */

    /**
     * @return LazyIterator
     */
    public function asTuples()
    {
        return LazyIterator::fromResult(function () {
            foreach ($this->iterator() as $k => $v) {
                yield [$k, $v];
            }
        });
    }
}
