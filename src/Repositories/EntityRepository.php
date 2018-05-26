<?php

namespace Ollieread\Articulate\Repositories;

use Ollieread\Articulate\Concerns;
use Ollieread\Articulate\Contracts\EntityRepository as Contract;
use Ollieread\Articulate\EntityManager;
use Ollieread\Articulate\Mapping;

/**
 * Class EntityRepository
 *
 * @package Ollieread\Articulate\Repositories
 */
abstract class EntityRepository implements Contract
{
    use Concerns\HandlesEntities;

    /**
     * EntityRepository constructor.
     *
     * @param \Ollieread\Articulate\EntityManager $manager
     * @param \Ollieread\Articulate\Mapping       $mapping
     */
    public function __construct(EntityManager $manager, Mapping $mapping)
    {
        $this->setManager($manager)->setMapping($mapping);
    }
}