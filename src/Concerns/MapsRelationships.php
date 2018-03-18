<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Relationships\BaseRelationship;
use Ollieread\Articulate\Relationships\BelongsTo;
use Ollieread\Articulate\Relationships\HasMany;

/**
 * Trait MapsRelationships
 *
 * @package Ollieread\Articulate\Concerns
 */
trait MapsRelationships
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $relationships;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRelationships(): Collection
    {
        return $this->relationships;
    }

    /**
     * @param \Ollieread\Articulate\Relationships\BaseRelationship $relationship
     *
     * @return \Ollieread\Articulate\Relationships\BaseRelationship
     */
    public function mapRelationship(BaseRelationship $relationship): BaseRelationship
    {
        $this->relationships->put($relationship->getName(), $relationship);
        return $relationship;
    }

    /**
     * @param string $relationship
     *
     * @return null|\Ollieread\Articulate\Relationships\BaseRelationship
     */
    public function getRelationship(string $relationship): ?BaseRelationship
    {
        return $this->relationships->get($relationship, null);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string      $name
     * @param string      $relatedEntity
     * @param string      $foreignKey
     * @param null|string $ownerKey
     *
     * @return \Ollieread\Articulate\Relationships\BaseRelationship
     */
    public function mapBelongsTo(string $name, string $relatedEntity, string $foreignKey, ?string $ownerKey = null): BaseRelationship
    {
        return $this->mapRelationship(new BelongsTo($name, $relatedEntity, $this->entity, $foreignKey, $ownerKey ?? 'id'));
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string      $name
     * @param string      $relatedEntity
     * @param string      $foreignKey
     * @param null|string $ownerKey
     *
     * @return \Ollieread\Articulate\Relationships\BaseRelationship
     */
    public function mapHasMany(string $name, string $relatedEntity, string $foreignKey, ?string $ownerKey = null): BaseRelationship
    {
        return $this->mapRelationship(new HasMany($name, $relatedEntity, $this->entity, $foreignKey, $ownerKey ?? 'id'));
    }
}