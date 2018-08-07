<?php

namespace Sprocketbox\Articulate\Sources\Basic;

use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\Repositories\Repository;

class BasicRepository extends Repository
{

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     *
     * @return mixed
     */
    public function persist(Entity $entity)
    {
        return $entity;
    }
}