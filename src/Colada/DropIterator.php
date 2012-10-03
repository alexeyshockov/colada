<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class DropIterator extends \IteratorIterator
{
    private $from;

    public function __construct($iterator, $from)
    {
        parent::__construct($iterator);

        $this->from = $from;
    }

    public function rewind()
    {
        parent::rewind();

        $found = false;
        while ($this->valid()) {
            if (ComparisonHelper::isEquals($this->current(), $this->from)) {
                $found = true;
            }

            $this->next();

            if ($found) {
                break;
            }
        }
    }
}
