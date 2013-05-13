<?php

/*
 * Something strange here with require_once. Boolean (true) is returned instead of class loader object.
 */
$loader = require __DIR__."/../vendor/autoload.php";

$loader->add('Colada\\', __DIR__);
