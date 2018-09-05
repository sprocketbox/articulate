<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\EntityManager;
use Sprocketbox\Articulate\Support\Collection;
use Sprocketbox\Respite\Contracts\Provider;
use Sprocketbox\Respite\Request\Builder;

/**
 * Class RespiteBuilder
 *
 * @mixin \Sprocketbox\Respite\Request\Builder
 *
 * @method RespiteBuilder get(string $uri, array $parameters = [])
 * @method RespiteBuilder post(string $uri, array $parameters = [])
 * @method RespiteBuilder delete(string $uri, array $parameters = [])
 * @method RespiteBuilder patch(string $uri, array $parameters = [])
 * @method RespiteBuilder headers(array $headers = [])
 * @method RespiteBuilder header(string $header, $value)
 * @method RespiteBuilder body(array $body = [])
 *
 * @package Sprocketbox\Articulate\Sources\Respite
 */
class RespiteBuilder
{
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

    public function __construct(Builder $builder, EntityManager $entityManager)
    {
        $this->builder = $builder;
        $this->manager = $entityManager;
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

    public function __call($name, $arguments): self
    {
        $this->builder->{$name}(...$arguments);
        return $this;
    }

    public function toBase()
    {
        return $this->builder;
    }

    public function many(?string $key = null)
    {
        $results = $this->builder->contents($key);
        $results = new Collection($results);

        if ($results && $results->count()) {
            return $results->map(function ($result) {
                return $this->manager->hydrate($this->getEntity(), $result);
            });
        }

        return $results;
    }

    /**
     * @param null|string $key
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity
     */
    public function one(?string $key = null)
    {
        $results = $this->builder->contents($key);

        if ($results) {
            return $this->manager->hydrate($this->getEntity(), $results);
        }

        return null;
    }
}