<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Contracts
{
    public static function ensureCallable()
    {
        foreach (func_get_args() as $value) {
            if (!is_callable($value)) {
                throw new \InvalidArgumentException('Argument must be callable.');
            }
        }
    }

    public static function ensureNotEmpty()
    {
        foreach (func_get_args() as $value) {
            if (count($value) == 0) {
                throw new \InvalidArgumentException('Collection must not be empty.');
            }
        }
    }

    public static function ensureInteger()
    {
        foreach (func_get_args() as $value) {
            if (!is_int($value)) {
                throw new \InvalidArgumentException('Argument must be integer.');
            }
        }
    }

    public static function ensurePositiveNumber()
    {
        foreach (func_get_args() as $value) {
            if (!(is_numeric($value) && $value >= 0)) {
                throw new \InvalidArgumentException('Argument must be positive.');
            }
        }
    }
}
