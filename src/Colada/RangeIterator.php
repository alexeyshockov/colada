<?php

namespace Colada;

/**
 * @see xrange()
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class RangeIterator implements \Iterator
{
    private $start, $end, $step;

    private $n;

    /**
     * @param int  $start
     * @param int $end
     * @param int  $step
     */
    public function __construct($start = 0, $end = null, $step = 1)
    {
        if (func_num_args() == 1) {
            $stop  = $start;
            $start = 0;
        }

        $this->start = $start;
        $this->end   = $end;
        $this->step  = $step;

        $this->n = $start;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->n;
    }

    /**
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ($this->end) {
            if (!($this->n < $this->end)) {
                // Is this acceptable?
                $this->n = null;
            }
        } else {
            $this->n += $this->step;
        }
    }

    /**
     * @return scalar Scalar on success, or null on failure.
     */
    public function key()
    {
        // Is this acceptable?
        return null;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        if ($this->end) {
            return ($this->n < $this->end);
        }

        return true;
    }

    /**
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->n = $this->start;
    }
}
