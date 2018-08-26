<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\Contracts\Resolver;
use Sprocketbox\Articulate\Entities\Entity;

class BelongsTo implements Resolver
{
    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var string
     */
    protected $relatedKey;

    /**
     * @var bool
     */
    protected $cascade = true;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $relatedEntity;

    public function __construct(string $entity, string $relatedEntity, string $localKey, string $relatedKey = 'id')
    {
        $this->localKey      = $localKey;
        $this->relatedKey    = $relatedKey;
        $this->entity        = $entity;
        $this->relatedEntity = $relatedEntity;
    }

    /**
     * Get related entities
     *
     * @param string                         $attribute
     * @param \Illuminate\Support\Collection $data
     * @param \Closure|null                  $condition
     *
     * @return null|Collection
     */
    public function getRelated(string $attribute, Collection $data, ?\Closure $condition = null): ?Collection
    {
        $keys = $data->map(function ($row) {
            return $row[$this->localKey] ?? null;
        })->filter(function ($key) {
            return ! empty($key);
        });

        if ($keys->count() > 0) {
            $repository = entities()->repository($this->relatedEntity);

            if ($repository) {
                /**
                 * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
                 */
                $query = $repository->source()->builder($repository->entity(), $repository->mapping());

                if ($keys->count() > 1) {
                    $query->whereIn($this->relatedKey, $keys->toArray());
                } else {
                    $query->where($this->relatedKey, '=', $keys->first());
                }

                if ($condition) {
                    $condition($query);
                }

                $results = $query->get()->keyBy($this->relatedKey);

                return $data->map(function ($row) use ($results, $attribute) {
                    $row[$attribute] = $results->get($row[$this->localKey]);
                    return $row;
                });
            }
        }

        return null;
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity                                            $entity
     * @param \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection $related
     *
     * @return mixed
     */
    public function persistRelated(Entity $entity, $related): void
    {
    }

    /**
     * @param \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder                                                       $builder
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping|\Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping $entityMapping
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping|\Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping $relatedMapping
     *
     * @return void
     */
    public function hasRelated($builder, EntityMapping $entityMapping, EntityMapping $relatedMapping): void
    {
        $foreignKey = $relatedMapping->getTable() . '.' . $this->relatedKey;
        $localKey   = $entityMapping->getTable() . '.' . $this->localKey;

        $builder->select()
                ->from($relatedMapping->getTable())
                ->where($foreignKey, '=', $localKey);
    }

    /**
     * Whether or not persists should cascade.
     *
     * @return bool
     */
    public function shouldCascade(): bool
    {
        return $this->cascade;
    }

    public function dontCascade(): self
    {
        $this->cascade = false;
        return $this;
    }

    public function cascade(): self
    {
        $this->cascade = true;
        return $this;
    }

    /**
     * Get the local key for persisting.
     *
     * @return null|string
     */
    public function getLocalKey(): ?string
    {
        return $this->localKey;
    }

    /**
     * Get the entity that owns this resolver/relationship
     *
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Get the related entity for this relationship
     *
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}