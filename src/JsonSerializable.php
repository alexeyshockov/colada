<?php

if (interface_exists('JsonSerializable', false)) {
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
