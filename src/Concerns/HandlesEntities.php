<?php

namespace Sprocketbox\Articulate\Concerns;

use Sprocketbox\Articulate\Contracts\Source;
use Sprocketbox\Articulate\Support\Collection;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\EntityManager;

trait HandlesEntities
{
    /**
     * @var string
     */
    private $_entity;

    /**
     * @var \Sprocketbox\Articulate\EntityManager
     */
    private $_manager;

    /**
     * @var \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    private $_mapping;

    /**
     * @var \Sprocketbox\Articulate\Contracts\Source
     */
    private $_source;

    /**
     * @return \Sprocketbox\Articulate\EntityManager
     */
    public function manager(): EntityManager
    {
        return $this->_manager;
    }

    /**
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function mapping(): EntityMapping
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
     * @return \Sprocketbox\Articulate\Contracts\Source
     */
    public function source(): Source
    {
        return $this->_source;
    }

    /**
     * @param string $entity
     *
     * @return $this
     */
    protected function setEntity(string $entity): self
    {
        $this->_entity = $entity;

        return $this;
    }

    /**
     * @param \Sprocketbox\Articulate\EntityManager $manager
     *
     * @return $this
     */
    protected function setManager(EntityManager $manager): self
    {
        $this->_manager = $manager;

        return $this;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $mapping
     *
     * @return $this
     */
    protected function setMapping(EntityMapping $mapping): self
    {
        $this->_mapping = $mapping;
        $this->setEntity($mapping->getEntity());

        return $this;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Source $source
     *
     * @return \Sprocketbox\Articulate\Concerns\HandlesEntities
     */
    protected function setSource(Source $source): self
    {
        $this->_source = $source;

        return $this;
    }

    /**
     * @param             $result
     *
     * @param null|string $entity
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity|static|\Sprocketbox\Articulate\Support\Collection
     */
    public function hydrate($result, ?string $entity = null)
    {
        $entity = $entity ?? $this->entity();

        if ($result instanceof Collection) {
            return $result->map(function ($row) use ($entity) {
                return $this->hydrate($row, $entity);
            });
        }

        return $this->manager()->hydrate($entity, $result);
    }
}