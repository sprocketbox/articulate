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
interface ComponentMapping
{
    /**
     * @return string
     */
    public function getComponent(): string;

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
     * @param mixed ...$arguments
     *
     * @return \Sprocketbox\Articulate\Components\Component
     */
    public function make(...$arguments);
}