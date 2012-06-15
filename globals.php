<?php

/**
 * Colada classes for quick access in interactive CLI.
 */

class Collections extends \Colada\Collections {};

class CollectionBuilder extends \Colada\CollectionBuilder {};
class SetBuilder        extends \Colada\SetBuilder {};
class MapBuilder        extends \Colada\MapBuilder {};
class MiltimapBuilder   extends \Colada\MultimapBuilder {};

class IteratorCollection extends \Colada\IteratorCollection {};
class PairMap            extends \Colada\PairMap {};

class ArrayIteratorPairs    extends \Colada\ArrayIteratorPairs {};
class SplObjectStoragePairs extends \Colada\SplObjectStoragePairs {};

abstract class Option extends \Colada\Option {};
class Some   extends \Colada\Some {};
class None   extends \Colada\None {};
