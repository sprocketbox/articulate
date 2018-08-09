<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Sprocketbox\Articulate\Contracts\Criteria;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\Repositories\Repository;
use Sprocketbox\Articulate\Support\Collection;

/**
 * Class RespiteRepository
 *
 * @method RespiteBuilder applyCriteria(RespiteBuilder $query)
 *
 * @package Sprocketbox\Articulate\Sources\Respite
 */
class RespiteRepository extends Repository
{
    /**
     * @return RespiteBuilder
     */
    protected function builder(): RespiteBuilder
    {
        return $this->source()->builder($this->entity(), $this->mapping());
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Criteria ...$criteria
     *
     * @return \Sprocketbox\Articulate\Support\Collection
     */
    public function getByCriteria(Criteria... $criteria): Collection
    {
        collect($criteria)->each([$this, 'pushCriteria']);
        return $this->applyCriteria($this->builder())->many() ?? new Collection;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Criteria ...$criteria
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity
     */
    public function getOneByCriteria(Criteria... $criteria): ?Entity
    {
        collect($criteria)->each([$this, 'pushCriteria']);
        return $this->applyCriteria($this->builder())->one();
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     *
     * @return mixed
     */
    public function persist(Entity $entity)
    {
        return null;
    }
}