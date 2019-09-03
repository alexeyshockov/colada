<?php

namespace Colada;

use Carbon\CarbonInterval;
use DateInterval;
use Iterator;
use RuntimeException;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * @api
 */
final class ProcessingStopwatch
{
    /** @var StopwatchEvent */
    private $swEvent;

    /** @var int */
    private $processed;

    /** @var int */
    private $position;

    /** @var ProcessingStopwatchLap[] */
    private $laps = [];

    /** @var callable */
    private $lapHandler;

    /** @var int */
    private $lapStep = PHP_INT_MAX;

    /** @var bool */
    private $stopped = false;

    public static function create(): ProcessingStopwatch
    {
        $position = $processed = 0;

        return new self(
            (new StopwatchEvent(hrtime(true)))->start(),
            $position,
            $processed
        );
    }

    public static function wrap(iterable $flow, int $step, callable $lapHandler = null): Iterator
    {
        $progress = self::create()
            ->lapEvery($step, $lapHandler);

        foreach ($flow as $key => $value) {
            yield $key => $value;

            $progress->tick();
        }

        $progress->stop();
    }

    public function __construct(StopwatchEvent $swEvent, int &$position, int &$processed)
    {
        $this->swEvent = $swEvent;
        $this->position = &$position;
        $this->processed = &$processed;
    }

    /**
     * @return $this
     */
    public function tick(): self
    {
        if ($this->stopped) {
            throw new RuntimeException('The stopwatch has been stopped already');
        }

        $this->position++;
        $this->processed++;

        if ($this->position % $this->lapStep === 0) {
            $handler = $this->lapHandler;
            $handler($this->lap());
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function lapEvery(int $step, callable $handler = null): self
    {
        $this->lapStep = $step;
        $this->lapHandler = $handler ?? static function () {
            // Null callable
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function stop(): self
    {
        if ($this->position % $this->lapStep !== 0) {
            $handler = $this->lapHandler;
            $handler($this->lap());
        }

        $this->stopped = true;

        return $this;
    }

    public function lap(): ProcessingStopwatchLap
    {
        $this->swEvent->stop();
        $periods = $this->swEvent->getPeriods();
        $period = array_pop($periods);

        $lap = $this->laps[] = new ProcessingStopwatchLap($this, $period, $this->processed, $this->position);

        $this->processed = 0;
        $this->swEvent->start();

        return $lap;
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function eta(int $total = null): DateInterval
    {
        $left = $total ? ($total - $this->position) : 0;

        return ms2interval($left * $this->averageElementTime());
    }

    private function averageElementTime(): float
    {
        return $this->swEvent->getDuration() / $this->position;
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function duration(): DateInterval
    {
        return ms2interval($this->swEvent->getDuration());
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function averageLapDuration(): DateInterval
    {
        return ms2interval($this->swEvent->getDuration() / (count($this->laps) + 1));
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function averageElementDuration(): DateInterval
    {
        return ms2interval($this->averageElementTime());
    }

    public function position(): int
    {
        return $this->position;
    }
}
