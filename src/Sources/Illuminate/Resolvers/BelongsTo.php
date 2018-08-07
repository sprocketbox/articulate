<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

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
     * @param array                                        $data
     * @param \Closure|null                                $condition
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     */
    public function get(Repository $repository, array $data = [], ?\Closure $condition = null)
    {
        $key = $data[$this->localKey] ?? null;

        if ($key) {
            /**
             * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
             */
            $query = $repository->source()->builder($repository->entity(), $repository->mapping());
            $query->where($this->foreignKey, '=', $key);

            if ($condition) {
                $condition($query);
            }

            return $query->first();
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