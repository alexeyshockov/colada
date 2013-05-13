<?php

if (
    !(version_compare(PHP_VERSION, '5.4.0') >= 0)
    ||
    interface_exists('JsonSerializable', false)
) {
    return;
}

/**
 * For compatibility with PHP 5.3.
 *
 * @ignore
 */
interface JsonSerializable
{
    /**
     * @return array
     */
    function jsonSerialize();
}
