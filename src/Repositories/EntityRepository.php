<?php

namespace Ollieread\Articulate\Repositories;

use Illuminate\Support\Collection;
use Ollieread\Articulate\EntityManager;
use Ollieread\Articulate\Mapping;

/**
 * Class EntityRepository
 *
 * @package Ollieread\Articulate\Repositories
 */
abstract class EntityRepository
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
    protected function manager(): EntityManager
    {
        return $this->_manager;
    }

    /**
     * @return \Ollieread\Articulate\Mapping
     */
    protected function mapping(): Mapping
    {
        return $this->_mapping;
    }

    /**
     * @return string
     */
    protected function entity(): string
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
     * @return null|\Ollieread\Articulate\Entities\BaseEntity|static|\Illuminate\Support\Collection
     * @throws \RuntimeException
     */
    protected function hydrate($result)
    {
        if ($result instanceof Collection) {
            return $result->map(function ($row) {
                return $this->manager()->hydrate($this->entity(), $row);
            });
        }

        return $this->manager()->hydrate($this->entity(), $result);
    }
}