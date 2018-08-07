<?php

namespace Sprocketbox\Articulate\Contracts;

use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\EntityManager;

/**
 * Class EntityRepository
 *
 * @package Sprocketbox\Articulate\Repositories
 */
interface Repository
{
    /**
     * @return \Sprocketbox\Articulate\EntityManager
     */
    public function manager(): EntityManager;

    /**
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function mapping(): EntityMapping;

    /**
     * @return string
     */
    public function entity(): string;

    /**
     * @return \Sprocketbox\Articulate\Contracts\Source
     */
    public function source(): Source;

    /**
     * @param $result
     *
     * @return null|\Sprocketbox\Handle\Entities\Entity|static|\Sprocketbox\Articulate\Support\Collection
     * @throws \RuntimeException
     */
    public function hydrate($result);

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     *
     * @return mixed
     */
    public function persist(Entity $entity);
}