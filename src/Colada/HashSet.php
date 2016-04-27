<?php

namespace Colada;

use IteratorAggregate;
use Countable;
use JsonSerializable;
use ArrayObject;
use Traversable;

class HashSet implements IteratorAggregate, IterableAgain, Countable, JsonSerializable
{
    use IterableLike;
    use MonadLike;

    /**
     * Holds callable to create new instance
     */
    const unit = __CLASS__.'::unit';

    /** @var ArrayObject */
    protected $memory;

    /** @var callable */
    private $hasher;

    /**
     * @return Builder
     */
    public static function builder($hasher = null)
    {
        return new Builder(function () use ($hasher) {
            $set = new static([], $hasher);
            // Real hash function, with default fallback.
            $hasher = $set->hasher;

            $t = yield;
            while (count($t) === 2) {
                list($k, $v) = $t;

                $set->memory[(string) $hasher($v)] = $v;

                $t = yield;
            }

            // Generator::getReturn() from PHP 7 is better, but it's not available in previous versions.
            yield $set;
        });
    }

    /**
     * @param callable $source
     * @param callable|null $hasher function($value): string
     *
     * @return static
     */
    public static function fromResult(callable $source, callable $hasher = null)
    {
        return new static($source(), $hasher);
    }

    /**
     * @param Traversable|array $source
     * @param callable|null $hasher function($value): string
     */
    public function __construct($source = [], callable $hasher = null)
    {
        InvalidArgumentException::assertTraversable($source);

        $this->memory = new ArrayObject();
        $this->hasher = $hasher ?: any_hash;

        // To call it without call_user_func().
        $hasher = $this->hasher;
        foreach ($source as $v) {
            $this->memory[(string) $hasher($v)] = $v;
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
        return $this->hasher;
    }

    /**
     * @return LazyIterator
     */
    public function getIterator()
    {
        return new LazyIterator($this->iterator());
    }

    public function count()
    {
        return $this->memory->count();
    }

    /*
     * Overridden methods.
     */

    protected function iterator()
    {
        $i = function () {
            // Without keys (hashes).
            foreach ($this->memory->getIterator() as $v) {
                yield $v;
            }
        };

        return $i();
    }

    protected function modify(callable $modifier)
    {
        // To preserve hash function.
        return new static($modifier($this->internalIterator()), $this->getHasher());
    }

    public function toArray()
    {
        return iterator_to_array($this->iterator());
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
