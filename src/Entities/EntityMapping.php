<?php

namespace Sprocketbox\Articulate\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Contracts\EntityMapping as Contract;

/**
 * Class Mapping
 *
 * @mixin Concerns\MapsAttributes
 *
 * @package Sprocketbox\Articulate
 */
class EntityMapping implements Contract
{
    use Concerns\MapsAttributes,
        Macroable {
        Macroable::__call as macroCall;
        Concerns\MapsAttributes::__call as attributeCall;
    }

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $repository;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var null|\Closure
     */
    protected $inheritenceCase;

    /**
     * @var array
     */
    protected $childClasses = [];

    public function __construct(string $entity, string $source)
    {
        $this->entity     = $entity;
        $this->source     = $source;
        $this->attributes = new Collection;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|\Sprocketbox\Articulate\Contracts\Attribute
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (self::hasMacro($name)) {
            return $this->macroCall($name, $arguments);
        }

        return $this->attributeCall($name, $arguments);
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepository(): ?string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     *
     * @return self
     */
    public function setRepository(string $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function make(...$arguments)
    {
        $entityClass = $this->getEntity();

        if ($this->hasMultipleInheritance()) {
            $inheritance = $this->inheritenceCase;
            $entityClass = $inheritance(...$arguments);
        }

        return new $entityClass(...$arguments);
    }

    /**
     * @param \Closure $case
     *
     * @return \Sprocketbox\Articulate\Entities\EntityMapping
     */
    public function setMultipleInheritance(\Closure $case): self
    {
        $this->inheritenceCase = $case;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMultipleInheritance(): bool
    {
        return null !== $this->inheritenceCase;
    }

    /**
     * @param string ...$childEntities
     *
     * @return \Sprocketbox\Articulate\Entities\EntityMapping
     */
    public function setChildClasses(string ...$childEntities): self
    {
        $this->childClasses = $childEntities;
        return $this;
    }

    /**
     * @return array
     */
    public function getChildClasses(): array
    {
        return $this->childClasses;
    }
}