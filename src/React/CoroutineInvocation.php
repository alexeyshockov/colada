<?php

namespace Colada\React;

use Generator;
use React\Promise\CancellablePromiseInterface;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use React\Promise\PromisorInterface;
use RuntimeException;
use Throwable;
use function React\Promise\all;

/**
 * Wrapper for a \Generator to represent a coroutine
 *
 * The difference with the Guzzle's implementation is the resulting value: here it's taken from generator's return, and
 * Guzzle uses the last yeilded result.
 *
 * @see https://github.com/guzzle/promises/pull/51
 */
final class CoroutineInvocation implements PromisorInterface
{
    /**
     * @var PromiseInterface|null
     */
    private $currentPromise;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Deferred
     */
    private $result;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
        $this->result = new Deferred(function () {
            if ($this->currentPromise instanceof CancellablePromiseInterface) {
                $this->currentPromise->cancel();
            }
        });
        $this->next($this->generator->current());
    }

    public function promise()
    {
        return $this->result->promise();
    }

    private function handle($yielded): PromiseInterface
    {
        if (is_object($yielded) && ($yielded instanceof Generator)) {
            // Threat the value like an already running coroutine
            $yielded = (new static($yielded))->promise();
        } elseif (is_iterable($yielded)) {
            $promises = [];
            foreach ($yielded as $key => $item) {
                $promises[$key] = $this->handle($item);
            }

            $yielded = all($promises);
        }

        return $yielded instanceof PromiseInterface ? $yielded : new FulfilledPromise($yielded);
    }

    private function next($yielded)
    {
        return $this->currentPromise = $this->handle($yielded)->then($this->successHandler(), $this->failureHandler());
    }

    private function successHandler()
    {
        return function ($value) {
            unset($this->currentPromise);
            try {
                $next = $this->generator->send($value);

                if (!$this->generator->valid()) {
                    $this->result->resolve($this->generator->getReturn());
                } else {
                    $this->next($next);
                }
            } catch (Throwable $throwable) {
                $this->result->reject($throwable);
            }
        };
    }

    private function failureHandler()
    {
        return function ($reason) {
            unset($this->currentPromise);
            try {
                $nextYield = $this->generator->throw(
                    $reason instanceof Throwable ? $reason : new RuntimeException($reason)
                );
                // The throw was caught, so keep iterating on the coroutine
                $this->next($nextYield);
            } catch (Throwable $throwable) {
                $this->result->reject($throwable);
            }
        };
    }
}
