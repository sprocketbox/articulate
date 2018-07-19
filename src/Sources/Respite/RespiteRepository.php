<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Sprocketbox\Articulate\Repositories\Repository;
use Sprocketbox\Respite\Request\Builder;

class RespiteRepository extends Repository
{
    /**
     * @return \Sprocketbox\Articulate\Sources\Respite\RespiteBuilder
     */
    protected function builder(): RespiteBuilder
    {
        return $this->source()->builder($this->entity(), $this->mapping());
    }

    /**
     * @param \Sprocketbox\Respite\Request\Builder $builder
     *
     * @param null|string                          $key
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getOne(Builder $builder, ?string $key = null)
    {
        $results = $builder->contents();

        return $this->hydrate($key ? data_get($results, $key) : $results);
    }

    /**
     * @param \Sprocketbox\Respite\Request\Builder $builder
     * @param null|string                          $key
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function get(Builder $builder, ?string $key = null)
    {
        $results = $builder->contents();

        return $this->hydrate(collect($key ? data_get($results, $key) : $results));
    }
}