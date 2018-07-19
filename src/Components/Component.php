<?php

namespace Sprocketbox\Articulate\Components;

use Sprocketbox\Articulate\Concerns;

/**
 * Class Component
 *
 * @package Sprocketbox\Articulate\Components
 */
abstract class Component implements \ArrayAccess, \JsonSerializable
{
    use Concerns\HasAttributes;
}