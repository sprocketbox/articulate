<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Contracts\Mapping;
use Ollieread\Articulate\EntityManager;

trait HandlesEntities
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
     * @var \Ollieread\Articulate\Contracts\Mapping
     */
    private $_mapping;

    /**
     * @return \Ollieread\Articulate\EntityManager
     */
    public function manager(): EntityManager
    {
        return $this->_manager;
    }

    /**
     * @return \Ollieread\Articulate\Contracts\Mapping
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
     * @param \Ollieread\Articulate\EntityManager $manager
     *
     * @return $this
     */
    protected function setManager(EntityManager $manager): self
    {
        $this->_manager = $manager;

        return $this;
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Mapping $mapping
     *
     * @return $this
     */
    protected function setMapping(Mapping $mapping): self
    {
        $this->_mapping = $mapping;
        $this->setEntity($mapping->getEntity());

        return $this;
    }

    /**
     * @param             $result
     *
     * @param null|string $entity
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity|static|\Illuminate\Support\Collection
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