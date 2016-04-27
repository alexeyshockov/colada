<?php

namespace Colada;

/**
 * Builder for simple arrays
 *
 * Useful with Iterable::unzip(), for example.
 */
class ArrayBuilder extends Builder
{
    public function __construct()
    {
        parent::__construct(function () {
            $array = [];

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
            yield $array;
        });
    }
}
