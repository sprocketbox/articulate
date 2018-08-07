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
     * @var array
     */
    protected $childEntityMappings = [];

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

        if ($mapping->hasMultipleInheritance()) {
            foreach ($mapping->getChildClasses() as $childClass) {
                $this->childEntityMappings[$childClass] = $entityClass;
            }
        }

        return $this;
    }

    /**
     * @param string $entityClass
     *
     * @return bool
     */
    public function hasEntityMapping(string $entityClass): bool
    {
        if (\array_key_exists($entityClass, $this->childEntityMappings)) {
            return $this->hasEntityMapping($this->childEntityMappings[$entityClass]);
        }

        return $this->entityMappings->has($entityClass);
    }

    /**
     * @param string $entityClass
     *
     * @return null|\Sprocketbox\Articulate\Entities\EntityMapping
     */
    public function getEntityMapping(string $entityClass): ?EntityMapping
    {
        if (! $this->hasEntityMapping($entityClass)) {
            throw new \InvalidArgumentException(sprintf('No mapping for entity %s', $entityClass));
        }

        if (\array_key_exists($entityClass, $this->childEntityMappings)) {
            return $this->getEntityMapping($this->childEntityMappings[$entityClass]);
        }

        return $this->entityMappings->get($entityClass);
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
        if (! $this->getComponentMapping($componentClass)) {
            throw new \InvalidArgumentException(sprintf('No mapping for component %s', $componentClass));
        }

        return $this->componentMappings->get($componentClass, null);
    }
}