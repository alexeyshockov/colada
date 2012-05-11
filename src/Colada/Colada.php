<?php

namespace Colada;

/**
 * _() function registrator (for quick access to “future” variables).
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Colada
{
    /**
     * Register \Colada\_() function for quick access to “future” variables.
     */
    public static function registerFunction()
    {

    }
}

/**
 * Some useful examples:
 *
 * <code>
 * $collection->acceptBy(_()->getName()->startsWith('Test'));
 * </code>
 *
 * vs.
 *
 * <code>
 * $collection->acceptBy(
 *     function($user) { return StringHelper::startsWith($user->getName(), 'Test'); }
 * );
 * </code>
 *
 * @return \Colada\X\FutureValue
 */
function _()
{
    return new \Colada\X\FutureValue();
}
