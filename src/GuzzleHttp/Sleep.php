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
        $this->start = $this->currentTime();

        $this->promise = new Promise($this, [$this, 'stop']);
    }

    private function currentTime()
    {
        return function_exists('hrtime') ? hrtime(true) : microtime(true);
    }

    /** @internal */
    public function __invoke()
    {
        if ($this->stopped) {
            return;
        }

        if ((($this->currentTime() - $this->start) * 1e+6) > $this->sleepTime) {
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

        $diff = $this->currentTime() - $this->start;

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
