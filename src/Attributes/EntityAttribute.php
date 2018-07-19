<?php

namespace Sprocketbox\Articulate\Attributes;

use Sprocketbox\Articulate\Support\Collection;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\EntityManager;

/**
 * Class EntityColumn
 *
 * @package Sprocketbox\Articulate\Attributes
 */
class EntityAttribute extends BaseAttribute
{
    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var bool
     */
    protected $multiple;

    /**
     * @var bool
     */
    protected $load = false;

    /**
     * EntityColumn constructor.
     *
     * @param string $attributeName
     * @param string $componentClass
     * @param bool   $multiple
     */
    public function __construct(string $attributeName, string $componentClass, bool $multiple = false)
    {
        parent::__construct($attributeName);
        $this->entityClass = $componentClass;
        $this->multiple    = $multiple;
    }

    /**
     * @param $value
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection|null
     * @throws \RuntimeException
     */
    public function cast($value)
    {
        if (! $value || $value instanceof $this->entityClass) {
            return $value;
        }

        if (is_scalar($value)) {
            $repository = app(EntityManager::class)->repository($this->entityClass);

            if ($this->load && $repository) {
                return $repository->load($value);
            }

            return null;
        }

        if (\is_array($value) && \is_array(array_first($value))) {
            $value = collect($value);
        }

        if ($this->multiple && $value instanceof Collection) {
            return $value->map(function ($entity) {
                return $this->cast($entity);
            });
        }

        return app(EntityManager::class)->hydrate($this->entityClass, $value);
    }

    /**
     * @param $value
     *
     * @return string|null
     * @throws \RuntimeException
     */
    public function parse($value): ?string
    {
        if ($this->multiple) {
            return null;
        }

        $mapping = app(EntityManager::class)->getMapping($this->entityClass);

        return $value instanceof Entity ? $value->get($mapping->getKey()) : $value;
    }

    /**
     * @param bool $load
     *
     * @return $this
     */
    public function setLoad(bool $load): self
    {
        $this->load = $load;

        return $this;
    }
}