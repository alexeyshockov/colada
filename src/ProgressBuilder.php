<?php

namespace Colada;

use Countable;
use Iterator;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * @api
 */
class ProgressBuilder
{
    /** @var int|null */
    private $total;

    /** @var int */
    private $lapSize = 10;

    /** @var callable|null */
    private $lapHandler;

    public function setTotal(int $total): ProgressBuilder
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param array|Countable $flow
     *
     * @return $this
     */
    public function setTotalFrom($flow): ProgressBuilder
    {
        if (is_array($flow) || (is_object($flow) && $flow instanceof Countable)) { // is_countable() from PHP 7.3+
            $this->total = count($flow);
        } else {
            throw new \InvalidArgumentException('Array or \\Countable object');
        }

        return $this;
    }

    private function swEvent(): StopwatchEvent
    {
        return new StopwatchEvent(
            function_exists('hrtime') ? hrtime(true) : microtime(true)
        );
    }

    public function setLapSize(int $lapSize)
    {
        $this->lapSize = $lapSize;

        return $this;
    }

    public function setLapHandler(callable $action)
    {
        $this->lapHandler = $action;

        return $this;
    }

    public function startFor(iterable $flow, callable $lapHandler = null): Iterator
    {
        // Freeze the state by copying the values to local variables
        $step = $this->lapSize;
        $position = 0;
        $processed = 0;
        $lapHandler = $lapHandler ?: $this->lapHandler ?: function (...$args) {
            /* Null callable */
        };

        $progress = new Progress($swEvent = $this->swEvent(), $position, $processed);
        $progress->setTotal($this->total);

        $swEvent->start();
        foreach ($flow as $key => $value) {
            yield $key => $value;

            $processed++;
            $position++;

            if ($position % $step === 0) {
                $lapHandler($progress->lap());
            }
        }

        if ($position % $step !== 0) {
            $lapHandler($progress->lap());
        }
    }
}
