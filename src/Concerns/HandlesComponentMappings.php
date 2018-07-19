<?php

namespace Sprocketbox\Articulate\Concerns;

use Sprocketbox\Articulate\Contracts\ComponentMapping;
use Sprocketbox\Articulate\Contracts\EntityMapping;

/**
 * Trait HandlesMappings
 *
 *
 *
 * @package Sprocketbox\Articulate\Concerns
 */
trait HandlesComponentMappings
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $componentMappings;

    /**
     * @param string                                             $component
     * @param \Sprocketbox\Articulate\Contracts\ComponentMapping $mapping
     *
     * @return self
     */
    protected function registerComponentMapping(string $component, ComponentMapping $mapping): self
    {
        if ($this->componentMappings->has($component)) {
            throw new \RuntimeException('Component already registered');
        }

        $this->componentMappings->put($component, $mapping);

        return $this;
    }

    /**
     * @param string $component
     *
     * @return bool
     */
    public function hasComponentMapping(string $component): bool
    {
        return $this->componentMappings->has($component);
    }

    /**
     * @param string $component
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    protected function getComponentMapping(string $component): EntityMapping
    {
        return $this->componentMappings->get($component, null);
    }
}