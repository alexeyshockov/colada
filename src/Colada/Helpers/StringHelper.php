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
    /**
     * @param string $string
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith($string, $needle)
    {
        return (substr($string, 0, strlen($needle)) === $needle);
    }

    /**
     * @param string $string
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith($string, $needle)
    {
        $length = strlen($needle);
        if (0 == $length) {
            return true;
        }

        $start = $length * -1; // Negative.

        return (substr($string, $start) === $needle);
    }

    /**
     * @param string          $string
     * @param string|RegExp   $pattern
     * @param string|callback $replace
     *
     * @return string
     */
    public static function replace($string, $pattern, $replace)
    {
        if (is_object($pattern) && ($pattern instanceof \Colada\RegExp)) {
            if (is_callable($replace)) {
                return preg_replace_callback($pattern->getPattern(), $replace, $string);
            } else {
                return preg_replace($pattern->getPattern(), $replace, $string);
            }
        } else {
            return str_replace($pattern, $replace, $string);
        }
    }



    /**
     * @param string $string1
     * @param string $string2
     *
     * @return string
     */
    public static function append($string1, $string2)
    {
        return $string1.$string2;
    }

    /**
     * @param string $string1
     * @param string $string2
     *
     * @return string
     */
    public static function prepend($string1, $string2)
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
        if ($delimiter instanceof \Colada\RegExp) {
            return preg_split($delimiter->getPattern(), $string);
        } else {
            return explode($string, $delimiter);
        }
    }

    /**
     * @param string $string
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
    public static function isMatchBy($string, $pattern)
    {
        return preg_match($pattern, $string);
    }

    /**
     * Returns collection of matches (match - collection too, of concrete matches).
     *
     * Example:
     * <code>
     *
     * </code>
     *
     * @param $string
     * @param $pattern
     *
     * @return array
     */
    // TODO Return list (\Colada\Collection).
    public static function matches($string, $pattern)
    {
        preg_match_all($pattern, $string, $matches);

        return $matches;
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
        return (strlen(trim($string)) == 0);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function trim($string)
    {
        return trim($string);
    }
}
