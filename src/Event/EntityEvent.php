<?php

namespace Sprocketbox\Articulate\Event;

use Illuminate\Foundation\Events\Dispatchable;
use Sprocketbox\Articulate\Entities\Entity;

abstract class EntityEvent
{
    use Dispatchable;
    /**
     * @var \Sprocketbox\Articulate\Entities\Entity
     */
    protected $entity;

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(Entity $entity, array &$attributes)
    {
        $this->entity     = $entity;
        $this->attributes = $attributes;
    }

    public function entity(): Entity
    {
        return $this->entity;
    }

    public function &attributes(): array
    {
        return $this->attributes;
    }
}