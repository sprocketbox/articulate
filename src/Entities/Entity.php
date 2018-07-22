<?php

namespace Sprocketbox\Articulate\Entities;

use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Contracts\Attributeable;
use Sprocketbox\Articulate\Event\Hydrated;
use Sprocketbox\Articulate\Event\Hydrating;

/**
 * Class Entity
 *
 * @package Sprocketbox\Articulate\Entities
 */
abstract class Entity implements Attributeable, \ArrayAccess, \JsonSerializable
{
    use Concerns\HasAttributes;

    protected $persisted = false;

    public function isPersisted(): bool
    {
        return $this->persisted;
    }

    public function setPersisted()
    {
        $this->persisted = true;

        return $this;
    }

    public static function hydrating($attributeable, array $data): void
    {
        Hydrating::dispatch($attributeable, $data);
    }

    public static function hydrated($attributeable, array $data): void
    {
        Hydrated::dispatch($attributeable, $data);
    }
}