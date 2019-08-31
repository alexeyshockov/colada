<?php

namespace Colada\GuzzleHttp;

use Generator;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Invokes a generator function as a coroutine
 *
 * Creates a promise that is resolved using a generator that yields values or
 * promises (somewhat similar to C#'s async keyword).
 *
 *     use GuzzleHttp\Promise;
 *     use function Colada\GuzzleHttp\coroutine;
 *
 *     function createPromise($value) {
 *         return new Promise\FulfilledPromise($value);
 *     }
 *
 *     $promise = coroutine_invoke(function () {
 *         $value = (yield createPromise('a'));
 *         try {
 *             $value = (yield createPromise($value . 'b'));
 *         } catch (\Exception $e) {
 *             // The promise was rejected.
 *         }
 *         yield $value . 'c';
 *     });
 *
 *     // Outputs "abc"
 *     $promise->then(function ($v) { echo $v; });
 *
 * @api
 *
 * @param callable $generatorFn Generator function to wrap into a promise
 *
 * @return PromiseInterface
 */
function coroutine_invoke(callable $generatorFn): PromiseInterface
{
    return coroutine($generatorFn)();
}

/**
 * Wraps a generator function to produce a coroutine
 *
 * When the resulted function is called, the coroutine function will start an
 * instance of the generator and returns a promise that is fulfilled with its
 * final return value (see http://php.net/manual/en/generator.getreturn.php).
 *
 * Control is returned back to the generator when the yielded promise settles.
 * This can lead to less verbose code when doing lots of sequential async calls
 * with minimal processing in between.
 *
 *     use GuzzleHttp\Promise;
 *     use function Colada\GuzzleHttp\coroutine;
 *
 *     function createPromise($value) {
 *         return new Promise\FulfilledPromise($value);
 *     }
 *
 *     $coroutine = coroutine(function ($s1, $s2, $s3) {
 *         $value = (yield createPromise($s1));
 *         try {
 *             $value = (yield createPromise($value . $s2));
 *         } catch (\Exception $e) {
 *             // The promise was rejected.
 *         }
 *         yield $value . $s3;
 *     });
 *
 *     $promise = $coroutine('a', 'b', 'c');
 *
 *     // Outputs "abc"
 *     $promise->then(function ($v) { echo $v; });
 *
 * @api
 *
 * @param callable $generatorFn Generator function to wrap into a promise
 *
 * @return callable<PromiseInterface>
 */
function coroutine(callable $generatorFn): callable
{
    return function (...$args) use ($generatorFn) {
        // Proxy arguments to the generator function
        return (new CoroutineInvocation($generatorFn(...$args)))->promise();
    };
}

/**
 * Makes a coroutine (promise) from a generator instance
 *
 * @api
 *
 * @param Generator $gen A generator instance that represents a coroutine
 *
 * @return PromiseInterface
 */
function to_coroutine(Generator $gen)
{
    return (new CoroutineInvocation($gen))->promise();
}

/**
 * Concurrent version of \sleep()
 *
 * Like the internal function, but doesn't block the execution completely and allows other tasks in the queue to be
 * executed.
 *
 * @api
 *
 * @see \sleep()
 * @see \time_nanosleep()
 * @see \time_sleep_until()
 *
 * @param $seconds
 *
 * @return PromiseInterface
 */
function time_sleep($seconds)
{
    return (new Sleep($microseconds = $seconds * 1000 * 1000))->promise();
}
