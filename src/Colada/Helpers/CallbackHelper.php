<?php

namespace Colada\Helpers;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @internal
 */
class CallbackHelper
{
    /**
     * Invert boolean callbacks.
     *
     * @param callback $callback
     *
     * @return callback
     */
    public static function invert($callback)
    {
        return function() use($callback) {
            return !call_user_func_array($callback, func_get_args());
        };
    }
}
