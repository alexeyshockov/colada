<?php

namespace Colada\Helpers;

/**
 * @todo capitalize($string)
 * @todo replace($string, $pattern, $replace)
 *
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @internal
 */
class StringHelper
{
    public static function startsWith($string, $needle)
    {
        return (substr($string, 0, strlen($needle)) === $needle);
    }

    public static function endsWith($string, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        $start = $length * -1; // Negative.
        return (substr($string, $start) === $needle);
    }

    public static function split($string, $delimiter)
    {
        return explode($string, $delimiter);
    }

    public static function length($string)
    {
        return strlen($string);
    }

    /**
     * @param string $string
     * @param string $pattern
     *
     * @return bool
     */
    public static function match($string, $pattern)
    {
        return preg_match($pattern, $string);
    }

    /**
     * True if the string is null, or has nothing but whitespace characters, false otherwise.
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isBlank($string)
    {
        return (srtlen(trim($string)) == 0);
    }

    public function isEqualTo($string1, $string2)
    {
        return ($string1 == $string2);
    }

    public static function trim($string)
    {
        return trim($string);
    }
}
