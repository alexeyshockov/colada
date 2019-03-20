<?php

namespace Colada;

use Carbon\CarbonInterval;
use DateInterval;
use Symfony\Component\Stopwatch\StopwatchPeriod;

/**
 * @api
 */
final class ProgressLap
{
    /** @var Progress */
    private $progress;

    /** @var StopwatchPeriod */
    private $swPeriod;

    /** @var int */
    private $processed;

    /** @var int */
    private $position;

    public function __construct(Progress $progress, StopwatchPeriod $swPeriod, int $processed, int $position)
    {
        $this->progress = $progress;
        $this->swPeriod = $swPeriod;
        $this->processed = $processed;
        $this->position = $position;
    }

    public function overall(): Progress
    {
        return $this->progress;
    }

    /**
     * @return DateInterval|CarbonInterval
     */
    public function averageDuration(): DateInterval
    {
        return ms2interval($this->swPeriod->getDuration() / $this->processed);
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
