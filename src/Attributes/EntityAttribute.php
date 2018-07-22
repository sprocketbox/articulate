<?php

namespace Sprocketbox\Articulate\Attributes;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Entities\Entity;

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
     * @param       $value
     * @param array $data
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection|null
     */
    public function cast($value, array $data = [])
    {
        if (! $value || $value instanceof $this->entityClass) {
            return $value;
        }

        if (is_scalar($value)) {
            $repository = entities()->repository($this->entityClass);

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

        return entities()->hydrate($this->entityClass, $value);
    }

    /**
     * @param       $value
     * @param array $data
     *
     * @return string|null
     */
    public function parse($value, array $data = []): ?string
    {
        if ($this->multiple) {
            return null;
        }

        $mapping = entities()->getEntityMapping($this->entityClass);

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