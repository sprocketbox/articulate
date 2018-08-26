<?php

namespace Sprocketbox\Articulate\Components;

use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Contracts\Attributeable;

/**
 * Class Component
 *
 * @package Sprocketbox\Articulate\Components
 */
abstract class Component implements Attributeable, \ArrayAccess, \JsonSerializable
{
    use Concerns\HasAttributes;

    public static function hydrating($attributeable, array &$data): void
    {
    }

    public static function hydrated($attributeable, array $data): void
    {
    }
}