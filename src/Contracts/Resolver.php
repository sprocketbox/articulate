<?php

namespace Sprocketbox\Articulate\Contracts;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Entities\Entity;

interface Resolver
{
    /**
     * Get the entity that owns this resolver/relationship.
     *
     * @return string
     */
    public function getEntity(): string;

    /**
     * Get the related entity for this relationship.
     *
     * @return string
     */
    public function getRelatedEntity(): string;

    /**
     * Whether or not persists should cascade.
     *
     * @return bool
     */
    public function shouldCascade(): bool;

    /**
     * Get the local key for persisting.
     * A null value means that this relationship has no local key.
     *
     * @return null|string
     */
    public function getLocalKey(): ?string;

    /**
     * Get related entities.
     *
     * @param string                         $attribute
     * @param \Illuminate\Support\Collection $data
     * @param \Closure|null                  $condition
     *
     * @return null|Collection
     */
    public function getRelated(string $attribute, Collection $data, ?\Closure $condition = null): ?Collection;

    /**
     * Persist the related entities.
     *
     * @param \Sprocketbox\Articulate\Entities\Entity                                            $entity
     * @param \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection $related
     *
     * @return void
     */
    public function persistRelated(Entity $entity, $related): void;

    /**
     * Check for the presence of related entities.
     *
     * @param                                                 $builder
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $entityMapping
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $relatedMapping
     *
     * @return void
     */
    public function hasRelated($builder, EntityMapping $entityMapping, EntityMapping $relatedMapping): void;
}