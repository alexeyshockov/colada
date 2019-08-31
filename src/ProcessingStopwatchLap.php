<?php

namespace Colada;

use Carbon\CarbonInterval;
use DateInterval;
use Symfony\Component\Stopwatch\StopwatchPeriod;

/**
 * @api
 */
final class ProcessingStopwatchLap
{
    /** @var ProcessingStopwatch */
    private $progress;

    /** @var StopwatchPeriod */
    private $swPeriod;

    /** @var int */
    private $processed;

    /** @var int */
    private $position;

    public function __construct(ProcessingStopwatch $progress, StopwatchPeriod $swPeriod, int $processed, int $position)
    {
        $this->progress = $progress;
        $this->swPeriod = $swPeriod;
        $this->processed = $processed;
        $this->position = $position;
    }

    public function overall(): ProcessingStopwatch
    {
        return $this->progress;
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function averageElementDuration(): DateInterval
    {
        return ms2interval($this->swPeriod->getDuration() / $this->processed);
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function duration(): DateInterval
    {
        return ms2interval($this->swPeriod->getDuration());
    }

    public function processed(): int
    {
        return $this->processed;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function stopwatch(): StopwatchPeriod
    {
        return $this->swPeriod;
    }
}
