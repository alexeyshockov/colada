<?php

/**
 * Shell helpers, for PHP in interactive mode.
 */

// Fucking PHP...
// And fucking error: "Ran out of opcode space! You should probably consider writing this huge script into a file!". WTF?!
function use_colada() {
    require_once __DIR__.'/globals.php';

    \Colada\Colada::registerFunctions();
}

// TODO If `php composer.php install` not executed yet, what then?
require_once __DIR__.'/vendor/autoload.php';


/**
 * "Normalize" collections to arrays, maps to pairs collections.
 *
 * @param mixed $value
 *
 * @return mixed
 */
function normalize_value($value)
{
    if ($value instanceof \Colada\Collection) {
        $value = $value->mapBy(__FUNCTION__);

        // Collection — as array.
        return $value->toArray();
    } elseif ($value instanceof \Colada\Map) {
        // Map — as collection of pairs.
        return normalize_value($value->asPairs());
    } elseif (is_array($value) || ($value instanceof \Traversable)) {
        $elements = array();

        // For all children...
        foreach ($value as $element) {
            $element = normalize_value($element);

            $elements[] = $element;
        }

        return $elements;
    } else {
        // The end.
        return $value;
    }
}

/**
 * @todo Use Doctrine debug?
 *
 * @param string $name
 * @param mixed  $value
 */
function dump_variable($name, $value)
{
    $value = normalize_value($value);

    $export = var_export($value, true);

    // More pretty.
    $export = str_replace('array (', 'array(', $export);

    $oneLineExport = $export;
    $oneLineExport = str_replace("\n", '', $oneLineExport);
    $oneLineExport = preg_replace("/array\(\s+/", 'array(', $oneLineExport);
    $oneLineExport = preg_replace("/,\s+/", ', ', $oneLineExport);
    $oneLineExport = preg_replace("/=\>\s+/", '=> ', $oneLineExport);
    $oneLineExport = preg_replace("/,\s*\)/", ')', $oneLineExport);

    // One line — only for small dumps.
    if (strlen($oneLineExport) <= 80) {
        $export = $oneLineExport;
    }

    echo '$'.$name.' = '.$export."\n";
}

/**
 * do() or debug() :)
 *
 * P.S. do — reserved PHP's word.
 *
 * @param mixed $expession
 */
function d($expression)
{
    static $number = 0;

    // Firstly, assign variable.
    $GLOBALS['var'.$number] = $expression;

    dump_variable('var'.$number, $expression);

    $number++;
}

/**
 * var_dump() short alias.
 */
function vd()
{
    return call_user_func_array('var_dump', func_get_args());
}
