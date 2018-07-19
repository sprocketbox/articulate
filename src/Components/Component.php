<?php

namespace Sprocketbox\Articulate\Components;

use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Entities\Entity;

/**
 * Class Component
 *
 * @package Sprocketbox\Articulate\Components
 */
abstract class Component implements \ArrayAccess, \JsonSerializable
{
    use Concerns\HasAttributes;

    /**
     * @var \Sprocketbox\Articulate\Entities\Entity
     */
    protected $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return \Sprocketbox\Articulate\Entities\Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }
}