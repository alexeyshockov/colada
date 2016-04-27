<?php

namespace Colada;

use Traversable;
use IteratorAggregate;
use Countable;
use JsonSerializable;
use ArrayObject;

class ArrayMap implements IteratorAggregate, IterableAgain, Countable, JsonSerializable
{
    use IterableLike;
    use MonadLike;

    /**
     * Holds callable to create new instance
     */
    const unit = __CLASS__.'::unit';

    /**
     * ArrayObject consumes the same memory as array type
     *
     * @var ArrayObject
     */
    protected $memory;

    /**
     * @return Builder
     */
    public static function builder()
    {
        return new Builder(function () {
            $array = new ArrayObject();

            $t = yield;
            while (count($t) === 2) {
                list($k, $v) = $t;

                // FIXME NULL keys!

                // TODO Catch it in builder...
                InvalidArgumentException::assertScalar($k);

                $array[$k] = $v;

                $t = yield;
            }

            // Generator::getReturn() from PHP 7 is better, but it's not available in previous versions.
            yield static::wrap($array);
        });
    }

    /**
     * @return Builder
     */
    public static function groupedBuilder()
    {
        return new Builder(function () {
            $array = new ArrayObject();

            $t = yield;
            while (count($t) === 2) {
                list($k, $v) = $t;

                // TODO Catch it in builder...
                InvalidArgumentException::assertScalar($k);

                // Array will be created automatically for the first value.
                $array[$k][] = $v;

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
     * @param ArrayObject $array
     *
     * @return static
     */
    public static function wrap(ArrayObject $array)
    {
        $collection = new static();
        $collection->memory = $array;

        return $collection;
    }

    /**
     * @param array|Traversable $source
     */
    public function __construct($source = [])
    {
        InvalidArgumentException::assertTraversable($source);

        $this->memory = new ArrayObject();
        foreach ($source as $k => $v) {
            // Only scalar keys.
            InvalidArgumentException::assertScalar($k);

            $this->memory[$k] = $v;
        }
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
        return $this->memory->getIterator();
    }

    public function toArray()
    {
        return $this->memory->getArrayCopy();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
