<?php

namespace Sprocketbox\Articulate\Repositories;

use Sprocketbox\Articulate\Entities\Entity;

class EntityRepository extends Repository
{

    /**
     * @param mixed $identifier
     *
     * @return null|\Sprocketbox\Handle\Entities\Entity
     */
    public function load($identifier)
    {
        return null;
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     *
     * @return null|\Sprocketbox\Handle\Entities\Entity
     */
    public function save(Entity $entity): ?Entity
    {
        return $entity;
    }
}