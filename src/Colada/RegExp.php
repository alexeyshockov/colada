<?php

namespace Colada;

/**
 * Regular expression descriptor. Functor for matching.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class RegExp
{
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = (string) $pattern;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Match.
     *
     * @param string $string
     *
     * @return bool
     */
    public function __invoke($string)
    {
        $string = (string) $string;

        return (bool) preg_match($this->pattern, $string);
    }
}
