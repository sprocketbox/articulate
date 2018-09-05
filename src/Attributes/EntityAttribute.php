<?php

namespace Sprocketbox\Articulate\Attributes;

use Sprocketbox\Articulate\Contracts\Resolver;
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
            return null;
        }

        if (\is_array($value) && \is_array(array_first($value))) {
            $value = collect($value);
        }

        return entities()->hydrate($this->entityClass, $value);
    }

    /**
     * @param       $value
     * @param array $data
     *
     * @return string|null
     */
    public function parse($value, array $data = [])
    {
        if ($this->multiple) {
            return $value;
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

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}