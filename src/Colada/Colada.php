<?php

namespace Colada;

/**
 * x(), set(), collection() and other functions registrator (for quick access to “future” variables).
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Colada
{
    private static $registered = false;

    /**
     * Register x(), set(), collection() and other functions for quick access to “future” variables.
     */
    public static function registerFunctions()
    {
        if (!static::$registered) {
            require_once __DIR__.'/../functions.php';

            static::$registered = true;
        }
    }
}
