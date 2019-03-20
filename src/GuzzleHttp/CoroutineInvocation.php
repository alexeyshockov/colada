<?php

namespace Colada\GuzzleHttp;

use Generator;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\PromisorInterface;
use RuntimeException;
use Throwable;
use function GuzzleHttp\Promise\all;
use function GuzzleHttp\Promise\promise_for;

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
     * @var Promise
     */
    private $result;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
        $this->result = new Promise(
            function () {
                while (isset($this->currentPromise)) {
                    $this->currentPromise->wait();
                }
            },
            function () {
                $this->currentPromise->cancel();
            }
        );
        $this->next($this->generator->current());
    }

    public function promise()
    {
        return $this->result;
    }

    private function handle($yielded)
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

        return promise_for($yielded);
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
