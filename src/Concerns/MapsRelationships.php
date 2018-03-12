<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Relationships\BaseRelationship;
use Ollieread\Articulate\Relationships\BelongsTo;
use Ollieread\Articulate\Relationships\HasMany;

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

    public function getRelationship(string $relationship): ?BaseRelationship
    {
        return $this->relationships->get($relationship, null);
    }

    public function mapBelongsTo(string $name, string $relatedEntity, string $foreignKey, ?string $ownerKey = null)
    {
        return $this->mapRelationship(new BelongsTo($name, $relatedEntity, $this->entity, $foreignKey, $ownerKey ?? 'id'));
    }

    public function mapHasMany(string $name, string $relatedEntity, string $foreignKey, ?string $ownerKey = null)
    {
        return $this->mapRelationship(new HasMany($name, $relatedEntity, $this->entity, $foreignKey, $ownerKey ?? 'id'));
    }
}