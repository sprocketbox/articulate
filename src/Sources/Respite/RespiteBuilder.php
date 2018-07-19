<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\EntityManager;
use Sprocketbox\Articulate\Support\Collection;
use Sprocketbox\Respite\Contracts\Provider;
use Sprocketbox\Respite\Request\Builder;

class RespiteBuilder
{
    /**
     * @var \Sprocketbox\Respite\Contracts\Provider
     */
    protected $provider;

    /**
     * @var \Sprocketbox\Respite\Request\Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var \Sprocketbox\Articulate\EntityManager
     */
    protected $manager;

    public function __construct(Provider $provider, Builder $builder, EntityManager $entityManager)
    {
        $this->provider = $provider;
        $this->builder  = $builder;
    }

    protected function newCollection($items = [])
    {
        return new Collection($items);
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function make(): Entity
    {
        $entityClass = $this->entity;

        return new $entityClass;
    }

    public function __call($name, $arguments)
    {
        return $this->builder->{$name}(...$arguments);
    }

    public function toBase()
    {
        return $this->builder;
    }
}