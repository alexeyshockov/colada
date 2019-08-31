<?php

namespace Colada\GuzzleHttp;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromisorInterface;
use function GuzzleHttp\Promise\queue;

class Sleep implements PromisorInterface
{
    /**
     * @see \microtime()
     * @see \hrtime()
     *
     * @var float
     */
    private $start;

    /**
     * Milliseconds (1 000 000 of a second)
     *
     * @see \usleep()
     *
     * @var int
     */
    private $sleepTime;

    /** @var Promise */
    private $promise;

    /** @var bool */
    private $stopped = false;

    public function __construct($sleepTime)
    {
        $this->sleepTime = $sleepTime;
        $this->start = hrtime(true);

        $this->promise = new Promise($this, [$this, 'stop']);
    }

    /** @internal */
    public function __invoke()
    {
        if ($this->stopped) {
            return;
        }

        if (((hrtime(true) - $this->start) * 1e+6) > $this->sleepTime) {
            $this->promise->resolve(true);

            return;
        }

        usleep(1000); // 1 millisecond

        queue()->add($this);
    }

    /** @internal */
    public function stop()
    {
        $this->stopped = true;

        $diff = hrtime(true) - $this->start;

        // Return an array like \time_nanosleep() and \time_sleep_until()
        $this->promise->reject([
            'seconds' => floor($diff),
            'nanoseconds' => (int) (($diff - floor($diff)) * 1e9),
        ]);
    }

    public function promise()
    {
        return $this->promise;
    }
}
