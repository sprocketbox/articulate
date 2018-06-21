<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Relationships\BelongsTo;
use Ollieread\Articulate\Relationships\HasMany;
use Ollieread\Articulate\Relationships\Relationship;

trait MapsRelationships
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $relationships;

    public function getRelationships(): Collection
    {
        return $this->relationships;
    }

    public function mapRelationship(Relationship $relationship): Relationship
    {
        $this->relationships->push($relationship);
        return $relationship;
    }

    public function belongsTo(string $foreignEntity, string $foreignKey)
    {
        return $this->mapRelationship(new BelongsTo($foreignEntity, $foreignKey, $this->entity));
    }

    public function hasMany(string $foreignEntity, string $columnName, string $foreignKey, ?string $localKey = null)
    {
        $relationship = new HasMany($foreignEntity, $columnName, $foreignKey, $localKey ?? $this->getKey());
        $this->mapColumn($relationship->getColumn());

        return $this->mapRelationship($relationship);
    }
}