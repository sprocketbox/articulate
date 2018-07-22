<?php

namespace Sprocketbox\Articulate\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface Mapping
 *
 * @mixin \Sprocketbox\Articulate\Concerns\MapsAttributes
 * 
 * @package Sprocketbox\Articulate\Contracts
 */
interface EntityMapping
{
    /**
     * @return string
     */
    public function getEntity(): string;

    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @return null|mixed
     */
    public function getKey(): string;

    /**
     * @param string $key
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function setKey(string $key);

    /**
     * @return null|string
     */
    public function getRepository(): ?string;

    /**
     * @param string $repository
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function setRepository(string $repository);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAttributes(): Collection;

    /**
     * @param \Sprocketbox\Articulate\Contracts\Attribute $type
     *
     * @return \Sprocketbox\Articulate\Contracts\Attribute
     */
    public function mapAttribute(Attribute $type): Attribute;

    /**
     * @param string $column
     *
     * @return null|\Sprocketbox\Articulate\Contracts\Attribute
     */
    public function getAttribute(string $column): ?Attribute;

    /**
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function setMultipleInheritance(\Closure $case);

    /**
     * @return bool
     */
    public function hasMultipleInheritance(): bool;

    public function setChildClasses(string ...$childEntities);

    public function getChildClasses(): array;
}