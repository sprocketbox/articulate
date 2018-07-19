<?php

namespace Sprocketbox\Articulate\Concerns;

use Sprocketbox\Articulate\Contracts\ComponentMapping;
use Sprocketbox\Articulate\Contracts\EntityMapping;

/**
 * Trait HandlesMappings
 *
 * @package Sprocketbox\Articulate\Concerns
 */
trait HandlesMappings
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $entityMappings;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $componentMappings;

    /**
     * @param string                                         $entityClass
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $mapping
     *
     * @return \Sprocketbox\Articulate\Concerns\HandlesMappings
     */
    public function registerEntityMapping(string $entityClass, EntityMapping $mapping): self
    {
        if ($this->hasEntityMapping($entityClass)) {
            throw new \InvalidArgumentException(sprintf('Entity %s is already mapped', $entityClass));
        }
        
        $this->entityMappings->put($entityClass, $mapping);
        return $this;
    }

    /**
     * @param string $entityClass
     *
     * @return bool
     */
    public function hasEntityMapping(string $entityClass): bool
    {
        return $this->entityMappings->has($entityClass);
    }

    /**
     * @param string $entityClass
     *
     * @return null|\Sprocketbox\Articulate\Entities\EntityMapping
     */
    public function getEntityMapping(string $entityClass): ?EntityMapping
    {
        return $this->entityMappings->get($entityClass, null);
    }

    /**
     * @param string                                              $componentClass
     * @param \Sprocketbox\Articulate\Contracts\ComponentMapping $mapping
     *
     * @return \Sprocketbox\Articulate\Concerns\HandlesMappings
     */
    public function registerComponentMapping(string $componentClass, ComponentMapping $mapping): self
    {
        if ($this->hasComponentMapping($componentClass)) {
            throw new \InvalidArgumentException(sprintf('Component %s is already mapped', $componentClass));
        }

        $this->componentMappings->put($componentClass, $mapping);
        return $this;
    }

    /**
     * @param string $componentClass
     *
     * @return bool
     */
    public function hasComponentMapping(string $componentClass): bool
    {
        return $this->componentMappings->has($componentClass);
    }

    /**
     * @param string $componentClass
     *
     * @return null|\Sprocketbox\Articulate\Components\ComponentMapping
     */
    public function getComponentMapping(string $componentClass): ?ComponentMapping
    {
        return $this->componentMappings->get($componentClass, null);
    }
}