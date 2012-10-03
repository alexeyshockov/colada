<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class TakeIterator extends \IteratorIterator
{
    private $to;

    private $found;

    public function __construct($iterator, $to)
    {
        parent::__construct($iterator);

        $this->found = false;
        $this->to    = $to;
    }

    public function next()
    {
        parent::next();

        // Scroll to the end...
        if ($this->found) {
            while ($this->valid()) {
                parent::next();
            }

            return;
        }

        if (ComparisonHelper::isEquals($this->current(), $this->to)) {
            $this->found = true;
        }
    }

    public function rewind()
    {
        parent::rewind();

        $this->found = false;
        if (ComparisonHelper::isEquals($this->current(), $this->to)) {
            $this->found = true;
        }
    }
}
