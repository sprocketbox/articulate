<?php

namespace Sprocketbox\Articulate\Entities;

use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Contracts\Attributeable;

/**
 * Class Entity
 *
 * @package Sprocketbox\Articulate\Entities
 */
abstract class Entity implements Attributeable, \ArrayAccess, \JsonSerializable
{
    use Concerns\HasAttributes;

    protected $persisted = false;

    public static function hydrated($entity): void
    {
    }

    public function isPersisted(): bool
    {
        return $this->persisted;
    }

    public function setPersisted()
    {
        $this->persisted = true;

        return $this;
    }
}