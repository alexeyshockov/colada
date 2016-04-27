<?php

namespace Colada;

use Traversable;
use Iterator;
use IteratorAggregate;
use Countable;
use EmptyIterator;
use SplObjectStorage;
use PhpOption\Option;
use PhpOption\None;
use PhpOption\Some;

class HashMap implements IteratorAggregate, IterableAgain, Countable
{
    use IterableLike;
    use MonadLike;

    /**
     * Holds callable to create new instance
     */
    const unit = __CLASS__.'::unit';

    /** @var ObjectStorage */
    protected $memory;

    /**
     * @param null|callable $hasher
     *
     * @return Builder
     */
    public static function builder(callable $hasher = null)
    {
        return new Builder(function () use ($hasher) {
            $map = new static(null, $hasher);

            $t = yield;
            while (count($t) === 2) {
                list($k, $v) = $t;

                InvalidArgumentException::assertObject($k);

                if (!isset($map->memory[$k])) {
                    $storage[$k] = [];
                }

                // Swap operation for SplObjectStorage... Ugly.
                $values = $map->memory[$k];
                $values[] = $v;
                $map->memory[$k] = $values;

                $t = yield;
            }

            // Generator::getReturn() from PHP 7 is better, but it's not available in previous versions.
            yield $map;
        });
    }

    /**
     * @param null|callable $hasher
     *
     * @return Builder
     */
    public static function groupedBuilder(callable $hasher = null)
    {
        return new Builder(function () use ($hasher) {
            $map = new static(null, $hasher);

            $t = yield;
            while (count($t) === 2) {
                list($k, $v) = $t;

                InvalidArgumentException::assertObject($k);

                $map->memory[$k] = $v;

                $t = yield;
            }

            // Generator::getReturn() from PHP 7 is better, but it's not available in previous versions.
            yield $map;
        });
    }

    /**
     * @param callable $source
     * @param callable|null $hasher
     *
     * @return static
     */
    public static function fromResult(callable $source, callable $hasher = null)
    {
        return new static($source(), $hasher);
    }

    /**
     * @param SplObjectStorage $storage
     *
     * @return static
     */
    public static function wrap(SplObjectStorage $storage)
    {
        $map = new static();
        $map->memory = $storage;

        return $map;
    }

    /**
     * New immutable map with passed entries
     *
     * @param Traversable $source
     * @param callable $hasher
     */
    public function __construct(Traversable $source = null, callable $hasher = null)
    {
        $this->memory = new ObjectStorage($hasher);

        // With default value.
        $source = $source ?: new EmptyIterator();
        foreach ($source as $k => $v) {
            // Only object keys.
            InvalidArgumentException::assertObject($k);

            $this->memory[$k] = $v;
        }
    }

    public function isTraversableAgain()
    {
        return true;
    }

    /**
     * @return callable
     */
    public function getHasher()
    {
        return ($this->memory instanceof ObjectStorage) ? $this->memory->getHasher() : 'spl_object_hash';
    }

    /**
     * Creates new iterator over the map.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        /*
         * We are not mutable, yes!
         *
         * PHP don't copy all storage when clone it. We can simply clone storage each time we want to iterate over it.
         */
        $storage = clone $this->memory;

        return LazyIterator::fromResult(function () use ($storage) {
            foreach ($storage as $k) {
                yield $k => $storage->getInfo();
            }
        });
    }

    public function count()
    {
        return $this->memory->count();
    }

    /**
     * @param mixed $key
     *
     * @return Option
     */
    // TODO Move to general interface.
    public function findByKey($key)
    {
        // This can be done with Colada\X...
//        return option_from_try(lazy($this->storage)[$key]);

        try {
            $option = new Some($this->get($key));
        } catch (UnexpectedValueException $error) {
            $option = None::create();
        }

        return $option;
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    // TODO Move to general interface.
    public function get($key)
    {
        try {
            /*
             * Possible errors:
             * 1. PHP warning, if key is not an object ("SplObjectStorage::offsetGet() expects parameter 1 to be object,
             *    integer given on line 1").
             * 2. UnexpectedValueException if the collection doesn't contain the key.
             */
            return $this->memory[$key];
        } catch (\UnexpectedValueException $error) {
            throw new UnexpectedValueException('Key not found.', 0, $error);
        }
    }

    /*
     * Overridden methods.
     */

    protected function iterator()
    {
        return $this->getIterator();
    }

    protected function internalIterator()
    {
        foreach ($this->memory as $k) {
            yield $k => $this->memory->getInfo();
        }
    }

    protected function modify(callable $modifier)
    {
        // To preserve hash function.
        return new static($modifier($this->internalIterator()), $this->getHasher());
    }
}
