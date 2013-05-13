<?php

namespace Colada;

/**
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
        return static::getMethodFrom($element1, 'isEqualTo')->mapBy(function($method) use($element2) {
            $parameters = $method->getParameters();

            if (empty($parameters)) {
                // May be func_get_args() inside...
                return true;
            } else {
                $parameter = $parameters[0];

                // NULL for non class parameters.
                if ($parameterClass = $parameter->getClass()) {
                    if (is_object($element2) && $parameterClass->isInstance($element2)) {
                        // Match by parameter type.
                        return true;
                    } else {
                        // Not match by parameter type.
                        return false;
                    }
                }

                // Parameter type not defined.
                return true;
            }
        })->orElse(false);
    }

    /**
     * @param object|string $value      Object or class name.
     * @param string        $methodName
     *
     * @return \Colada\Option
     */
    private static function getMethodFrom($value, $methodName)
    {
        if (is_object($value) && !($value instanceof \ReflectionClass)) {
            $class = new \ReflectionObject($value);
        } elseif (is_string($value)) {
            $class = new \ReflectionClass($value);
        }

        $method = null;
        if ($class->hasMethod($methodName)) {
            $method = $class->getMethod($methodName);
        } else {
            if ($parentClass = $class->getParentClass()) {
                $method = static::getMethodFrom($parentClass, $methodName);
            }
        }

        return Option::from($method);
    }
}
