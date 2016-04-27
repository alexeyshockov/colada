<?php

namespace Colada;

use Iterator;
use PhpOption\LazyOption;
use PhpOption\Some;
use PhpOption\None;

/**
 * Unsafe iterator (mutable)
 */
class LazyIterator implements Iterator, IterableOnce
{
    use IterableLike;
    use MonadLike;

    /**
     * Holds callable to create new instance
     */
    const unit = __CLASS__.'::unit';

    /** @var Iterator */
    private $iterator;

    public static function fromResult(callable $source)
    {
        return new static($source());
    }

    /**
     * @param Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function isTraversableAgain()
    {
        return false;
    }


    public function toArrayMap()
    {
        return new ArrayMap($this->iterator);
    }

    /**
     * @return LazyOption
     */
    public function arrayMapOption()
    {
        return new LazyOption(function () {
            try {
                $option = new Some($this->toArrayMap());
            } catch (InvalidArgumentException $error) {
                $option = None::create();
            }

            return $option;
        });
    }

    public function toHashMap(callable $hasher = null)
    {
        return new HashMap($this->iterator, $hasher);
    }

    /**
     * @param callable|null $hasher
     *
     * @return LazyOption
     */
    public function hashMapOption(callable $hasher = null)
    {
        return new LazyOption(function () use ($hasher) {
            try {
                $option = new Some($this->toHashMap($hasher));
            } catch (InvalidArgumentException $error) {
                $option = None::create();
            }

            return $option;
        });
    }

    public function groupToHashMap(callable $hasher = null)
    {
        return HashMap::groupedFrom($this->iterator, $hasher);
    }

    /**
     * @param callable|null $hasher
     *
     * @return LazyOption
     */
    public function groupedToHashMapOption(callable $hasher = null)
    {
        return new LazyOption(function () use ($hasher) {
            try {
                $option = new Some($this->groupedToHashMapOption($hasher));
            } catch (InvalidArgumentException $error) {
                $option = None::create();
            }

            return $option;
        });
    }

    public function toSequence()
    {
        return new Sequence($this->iterator);
    }

    /**
     * @return LazyOption
     */
    public function sequenceOption()
    {
        return new LazyOption(function () {
            try {
                $option = new Some($this->toSequence());
            } catch (InvalidArgumentException $error) {
                $option = None::create();
            }

            return $option;
        });
    }

    public function toHashSet()
    {
        return new HashSet($this->iterator);
    }

    /**
     * @return LazyOption
     */
    public function hashSetOption()
    {
        return new LazyOption(function () {
            try {
                $option = new Some($this->toHashSet());
            } catch (InvalidArgumentException $error) {
                $option = None::create();
            }

            return $option;
        });
    }

    public function toArray()
    {
        return iterator_to_array($this->iterator);
    }

    /**
     * @return LazyOption
     */
    public function arrayOption()
    {
        return new LazyOption(function () {
            try {
                $option = new Some($this->toArrayMap());
            } catch (InvalidArgumentException $error) {
                $option = None::create();
            }

            return $option;
        });
    }

    /**
     * MUTATES iterator state
     */
    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    // In foreach and all other function rewind() will be called first.
    public function rewind()
    {
        // Rewind is not supported. Like in NoRewindIterator.
    }

    /*
     * Overridden methods.
     */

    protected function iterator()
    {
        return $this->iterator;
    }
}
