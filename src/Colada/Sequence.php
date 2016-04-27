<?php

namespace Colada;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use SplFixedArray;
use Traversable;

class Sequence implements IteratorAggregate, IterableAgain, Countable, JsonSerializable
{
    use IterableLike;
    use MonadLike;

    /**
     * Holds callable to create new instance
     */
    const unit = __CLASS__.'::unit';

    /** @var SplFixedArray */
    protected $memory;

    /**
     * @return Builder
     */
    public static function builder()
    {
        return new Builder(function () {
            $size = 0;
            $array = new SplFixedArray($size);

            $t = yield;
            while (count($t) === 2) {
                $array->setSize(++$size);
                $array[$size - 1] = $t[1];

                $t = yield;
            }

            // Generator::getReturn() from PHP 7 is better, but it's not available in previous versions.
            yield static::wrap($array);
        });
    }

    /**
     * @param callable $source
     *
     * @return static
     */
    public static function fromResult(callable $source)
    {
        return new static($source());
    }

    /**
     * @param SplFixedArray $array
     *
     * @return static
     */
    public static function wrap(SplFixedArray $array)
    {
        $collection = new static([]);
        $collection->memory = $array;

        return $collection;
    }

    /**
     * @param Traversable|array $source
     */
    public function __construct($source = [])
    {
        InvalidArgumentException::assertTraversable($source);

        if (is_array($source)) {
            $array = SplFixedArray::fromArray($source);
        } else {
            $size = 0;
            $array = new SplFixedArray($size);
            foreach ($source as $v) {
                $array->setSize(++$size);
                $array[$size - 1] = $v;
            }
        }

        $this->memory = $array;
    }

    public function isTraversableAgain()
    {
        return true;
    }

    /**
     * @return LazyIterator
     */
    public function getIterator()
    {
        $array = clone $this->memory;

        return new LazyIterator($array);
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
        return $this->getIterator();
    }

    protected function internalIterator()
    {
        return $this->memory;
    }

    public function reversed()
    {
        return new static($this->reverseFixedArray($this->memory));
    }

    public function toArray()
    {
        return $this->memory->toArray();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
