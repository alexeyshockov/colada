<?php

namespace Colada;

use Generator;
use Traversable;

class Builder
{
    /** @var callable */
    private $function;

    /** @var Generator */
    private $generator;

    /**
     * @internal
     *
     * @param callable $function Generator constructor
     */
    public function __construct(callable $function)
    {
        $this->function = $function;

        $this->init();
    }

    private function init()
    {
        $this->generator = call_user_func($this->function);
    }

    /**
     * @param mixed $v
     * @param null|mixed $k
     *
     * @return $this
     */
    public function add($v, $k = null)
    {
        $this->addTuple([$k, $v]);

        return $this;
    }

    /**
     * @param callable $source
     *
     * @return $this
     */
    public function addFromResult(callable $source)
    {
        return $this->add($source());
    }

    /**
     * @param array $tuple
     *
     * @return $this
     */
    public function addTuple(array $tuple)
    {
        $this->generator->send($tuple);

        return $this;
    }

    /**
     * @param callable $source
     *
     * @return $this
     */
    public function addTupleFromResult(callable $source)
    {
        return $this->addTuple($source());
    }

    /**
     * @param array|Traversable $tuples
     *
     * @return $this
     */
    public function addTuples($tuples)
    {
        InvalidArgumentException::assertTraversable($tuples);

        foreach ($tuples as $t) {
            $this->addTuple($t);
        }

        return $this;
    }

    /**
     * @param callable $source
     *
     * @return $this
     */
    public function addTuplesFromResult(callable $source)
    {
        return $this->addTuples($source());
    }

    /**
     * @param array|Traversable $source
     *
     * @return $this
     */
    public function addAll($source)
    {
        InvalidArgumentException::assertTraversable($source);

        foreach ($source as $k => $v) {
            $this->add($v, $k);
        }

        return $this;
    }

    /**
     * @return Iterable
     */
    public function build()
    {
        $this->generator->send([]);
        $result = $this->generator->current();

        $this->init();

        return $result;
    }
}
