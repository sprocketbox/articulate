<?php

namespace Ollieread\Articulate\Repositories;

use Illuminate\Support\Collection;
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
    /**
     * @var string
     */
    private $_entity;

    /**
     * @var \Ollieread\Articulate\EntityManager
     */
    private $_manager;

    /**
     * @var \Ollieread\Articulate\Mapping
     */
    private $_mapping;

    /**
     * EntityRepository constructor.
     *
     * @param \Ollieread\Articulate\EntityManager $manager
     * @param \Ollieread\Articulate\Mapping       $mapping
     */
    public function __construct(EntityManager $manager, Mapping $mapping)
    {
        $this->_manager = $manager;
        $this->_mapping = $mapping;
        $this->_entity  = $mapping->getEntity();
    }

    /**
     * @return \Ollieread\Articulate\EntityManager
     */
    public function manager(): EntityManager
    {
        return $this->_manager;
    }

    /**
     * @return \Ollieread\Articulate\Mapping
     */
    public function mapping(): Mapping
    {
        return $this->_mapping;
    }

    /**
     * @return string
     */
    public function entity(): string
    {
        return $this->_entity;
    }

    /**
     * @param null|string $entity
     *
     * @return mixed
     */
    abstract protected function query(?string $entity = null);

    /**
     * @param $result
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity|static|\Illuminate\Support\Collection
     * @throws \RuntimeException
     */
    public function hydrate($result)
    {
        if ($result instanceof Collection) {
            return $result->map(function ($row) {
                return $this->manager()->hydrate($this->entity(), $row);
            });
        }

        return $this->manager()->hydrate($this->entity(), $result);
    }
}