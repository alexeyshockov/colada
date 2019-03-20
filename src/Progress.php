<?php

namespace Colada;

use Carbon\CarbonInterval;
use DateInterval;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * @api
 */
final class Progress
{
    /** @var StopwatchEvent */
    private $swEvent;

    /** @var int */
    private $processed;

    /** @var int|null */
    private $total;

    /** @var int */
    private $position;

    /** @var ProgressLap[] */
    private $laps = [];

    public function __construct(StopwatchEvent $swEvent, int &$position, int &$processed)
    {
        $this->swEvent = $swEvent;
        $this->position = &$position;
        $this->processed = &$processed;
    }

    public function setTotal(?int $total): Progress
    {
        $this->total = $total;

        return $this;
    }

    public function lap(): ProgressLap
    {
        $this->swEvent->stop();
        $periods = $this->swEvent->getPeriods();
        $period = array_pop($periods);

        $lap = $this->laps[] = new ProgressLap($this, $period, $this->processed, $this->position);

        $this->processed = 0;
        $this->swEvent->start();

        return $lap;
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function eta(): DateInterval
    {
        $left = $this->total ? ($this->total - $this->position) : 0;

        return ms2interval($left * $this->averageTime());
    }

    private function averageTime(): float
    {
        return $this->swEvent->getDuration() / $this->position;
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function averageDuration(): DateInterval
    {
        return ms2interval($this->averageTime());
    }

    public function total(): ?int
    {
        return $this->total;
    }

    public function position(): int
    {
        return $this->position;
    }
}
