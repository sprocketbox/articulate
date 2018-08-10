<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\Contracts\Repository;
use Sprocketbox\Articulate\Contracts\Resolver;

class BelongsTo implements Resolver
{
    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var string
     */
    protected $foreignKey;

    public function __construct(string $localKey, string $foreignKey = 'id')
    {
        $this->localKey   = $localKey;
        $this->foreignKey = $foreignKey;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Repository $repository
     * @param string                                       $attribute
     * @param array|\Illuminate\Support\Collection         $data
     * @param \Closure|null                                $condition
     *
     * @return array|\Sprocketbox\Articulate\Support\Collection
     */
    public function get(Repository $repository, string $attribute, $data = [], ?\Closure $condition = null)
    {
        if ($data instanceof Collection) {
            $key = $data->map(function ($row) {
                return $row[$this->localKey] ?? null;
            })->filter(function ($key) {
                return ! empty($key);
            });
        }

        if ($key) {
            /**
             * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
             */
            $query = $repository->source()->builder($repository->entity(), $repository->mapping());

            if (\is_array($key)) {
                $query->whereIn($this->foreignKey, $key);
            } else {
                $query->where($this->foreignKey, '=', $key);
            }

            if ($condition) {
                $condition($query);
            }

            $results = $query->get()->keyBy($this->foreignKey);

            return $data->map(function ($row) use ($results, $attribute) {
                $row[$attribute] = $results->get($row[$this->localKey]);
                return $row;
            });
        }

        return null;
    }

    /**
     * @param \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder                                                       $builder
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping|\Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping $localMapping
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping|\Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping $foreignMapping
     *
     * @return void
     */
    public function has($builder, EntityMapping $localMapping, EntityMapping $foreignMapping)
    {
        $foreignKey = $foreignMapping->getTable() . '.' . $this->foreignKey;
        $localKey   = $localMapping->getTable() . '.' . $this->localKey;

        $builder->select()
                ->from($foreignMapping->getTable())
                ->where($foreignKey, '=', $localKey);
    }
}