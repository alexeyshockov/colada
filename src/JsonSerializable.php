<?php

// FIXME Don't declare interface, if it's already exists.
if (
    !(version_compare(PHP_VERSION, '5.4.0') >= 0)
    ||
    interface_exists('JsonSerializable', false)
) {
    return;
}

/**
 * For compatibility with PHP 5.3.
 */
interface JsonSerializable
{
    /**
     * @return array
     */
    function jsonSerialize();
}
