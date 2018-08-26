<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Entities\Entity;

class BelongsToMany extends BelongsTo
{
    /**
     * @var string
     */
    public $pivotTable;

    /**
     * @var string
     */
    public $pivotKey;

    /**
     * @var string
     */
    public $relatedPivotKey;

    public function __construct(string $entity, string $relatedEntity, string $pivotTable, string $localKey, string $pivotKey, string $relatedPivotKey, string $relatedKey = 'id')
    {
        parent::__construct($entity, $relatedEntity, $localKey, $relatedKey);

        $this->pivotTable      = $pivotTable;
        $this->pivotKey        = $pivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
    }

    /**
     * Get related entities
     *
     * @param string        $attribute
     * @param Collection    $data
     * @param \Closure|null $condition
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
                 * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder       $query
                 * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping $mapping
                 */
                $mapping = $repository->mapping();
                $query   = $repository->source()->builder($repository->entity(), $mapping);

                $query->join($this->pivotTable, function (JoinClause $join) use ($mapping) {
                    $join->on($this->pivotTable . '.' . $this->relatedPivotKey, '=', $mapping->getTable() . '.' . $this->relatedKey);
                })->addSelect($this->pivotTable . '.' . $this->pivotKey);

                if ($keys->count() > 1) {
                    $query->whereIn($this->pivotTable . '.' . $this->pivotKey, $keys->toArray());
                } else {
                    $query->where($this->pivotTable . '.' . $this->pivotKey, '=', $keys->first());
                }

                if ($condition) {
                    $condition($query);
                }

                $results = $query->get()->keyBy($this->pivotTable . '.' . $this->pivotKey);

                return $data->map(function ($row) use ($results, $attribute) {
                    $row[$attribute] = $results->get($row[$this->localKey]);
                    return $row;
                });
            }
        }

        return null;
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity                                                  $entity
     * @param \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection|array $related
     */
    public function persistRelated(Entity $entity, $related): void
    {
        $repository = entities()->repository($this->relatedEntity);

        if ($repository) {
            if ($related instanceof Collection) {
                $related = $related->toArray();
            }

            $localKey = $entity->get($this->localKey);

            if (\is_array($related)) {
                array_walk($related, function (Entity $entity) use ($localKey, $repository) {
                    /**
                     * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
                     */
                    $query = $repository->source()->builder($repository->entity(), $repository->mapping());
                    $query->from($this->pivotTable)
                          ->insert([
                              $this->pivotKey        => $localKey,
                              $this->relatedPivotKey => $entity->get($this->relatedKey),
                          ]);
                });
            } else if ($related instanceof Entity) {
                /**
                 * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
                 */
                $query = $repository->source()->builder($repository->entity(), $repository->mapping());
                $query->from($this->pivotTable)
                      ->insert([
                          $this->pivotKey        => $localKey,
                          $this->relatedPivotKey => $entity->get($this->relatedKey),
                      ]);
            }
        }
    }

    public function getLocalKey(): ?string
    {
        return null;
    }
}