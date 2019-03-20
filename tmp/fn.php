<?php

namespace Colada;

/**
 * @api
 *
 * @param callable      $then
 * @param callable|null $otherwise
 *
 * @return callable
 */
function promise_handler(callable $then, callable $otherwise = null): callable
{
    return function ($promise) use ($then, $otherwise) {
        // Should work for all Promise\A compatible promise classes
        return $promise->then($then, $otherwise);
    };
}
