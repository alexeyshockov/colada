<?php

namespace Colada\Helpers;

/**
 * @todo capitalize($string)
 *
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
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

    /**
     * @param string          $string
     * @param string|RegExp   $pattern
     * @param string|callback $replace
     */
    public function replace($string, $pattern, $replace)
    {
        $string  = (string) $string;
        $replace = (string) $replace;

        if (is_object($pattern) && ($pattern instanceof \Colada\RegExp)) {
            if (is_callable($replace)) {
                return preg_replace_callback($pattern->getPattern(), $replace, $string);
            } else {
                return preg_replace($pattern->getPattern(), $replace, $string);
            }
        } else {
            $pattern = (string) $pattern;

            return str_replace($pattern, $replace, $string);
        }
    }

    /**
     * @param string $string1
     * @param string $string2
     *
     * @return string
     */
    public function append($string1, $string2)
    {
        return $string1.$string2;
    }

    /**
     * @param string $string1
     * @param string $string2
     *
     * @return string
     */
    public function prepend($string1, $string2)
    {
        return $string2.$string1;
    }

    /**
     * @todo Return collection?
     *
     * @param string $string
     * @param string|\Colada\RegExp $delimiter
     *
     * @return array
     */
    public static function split($string, $delimiter)
    {
        return explode($string, $delimiter);
    }

    /**
     * @param $string
     *
     * @return int
     */
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
