<?php

namespace Sprocketbox\Articulate\Concerns;

use Sprocketbox\Articulate\Contracts\EntityMapping;

/**
 * Trait HandlesMappings
 *
 *
 *
 * @package Sprocketbox\Articulate\Concerns
 */
trait HandlesEntityMappings
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $entityMappings;

    /**
     * @param string                                          $entity
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $mapping
     *
     * @return self
     */
    protected function registerEntityMapping(string $entity, EntityMapping $mapping): self
    {
        if ($this->mappings->has($entity)) {
            throw new \RuntimeException('Entity already registered');
        }

        $this->mappings->put($entity, $mapping);

        return $this;
    }

    /**
     * @param string $entity
     *
     * @return bool
     */
    public function hasEntityMapping(string $entity): bool
    {
        return $this->mappings->has($entity);
    }

    /**
     * @param string $entity
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    protected function getEntityMapping(string $entity): EntityMapping
    {
        return $this->mappings->get($entity, null);
    }
}