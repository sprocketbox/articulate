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
     * @var \Sprocketbox\Articulate\Contracts\Resolver|\Closure
     */
    protected $resolver;

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

    public function setResolver(Resolver $resolver): self
    {
        $this->resolver = $resolver;

        return $this;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getResolver(): ?Resolver
    {
        return $this->resolver;
    }

    public function shouldCascade(): bool
    {
        return $this->getResolver() ? $this->getResolver()->shouldCascade() : false;
    }

    public function getColumnName(): string
    {
        if ($this->getResolver()) {
            return $this->getResolver()->getLocalKey() ?? '';
        }

        return parent::getColumnName();
    }

    public function isDynamic(): bool
    {
        return parent::isDynamic() || null === $this->getResolver() || null === $this->getResolver()->getLocalKey();
    }
}