<?php

namespace Colada;

/**
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class ComparisonHelper
{
    /**
     * @param mixed $element1
     * @param mixed $element2
     *
     * @return bool
     */
    public static function isEquals($element1, $element2)
    {
        if (is_object($element1)) {
            if (static::isEquationPossibleFor($element1, $element2)) {
                return $element1->isEqualTo($element2);
            } else {
                return ($element1 === $element2);
            }
        }

        return ($element1 == $element2);
    }

    private static function isEquationPossibleFor($element1, $element2)
    {
        $class = new \ReflectionObject($element1);

        $possible = false;
        if ($class->hasMethod('isEqualTo')) {
            $parameters = $class->getMethod('isEqualTo')->getParameters();
            $parameter  = $parameters[0];

            // NULL for non class parameters.
            if ($parameterClass = $parameter->getClass()) {
                if (is_object($element2) && $class->isInstance($element2)) {
                    $possible = true;
                }
            }
        }

        return $possible;
    }
}
