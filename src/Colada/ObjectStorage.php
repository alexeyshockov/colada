<?php

namespace Colada;

use SplObjectStorage;

/**
 * @internal
 */
class ObjectStorage extends SplObjectStorage
{
    /** @var callable */
    private $hasher;

    /**
     * @param callable $hasher
     */
    public function __construct(callable $hasher = null)
    {
        $this->hasher = $hasher ?: 'spl_object_hash';
    }

    public function getHash($object)
    {
        return call_user_func($this->hasher, $object);
    }

    public function getHasher()
    {
        return $this->hasher;
    }
}
