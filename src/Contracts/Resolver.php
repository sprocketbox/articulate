<?php

namespace Sprocketbox\Articulate\Contracts;

interface Resolver
{
    /**
     * Retrieve the entity.
     *
     * @param \Sprocketbox\Articulate\Contracts\Repository $repository
     * @param string                                       $attribute
     * @param array|\Illuminate\Support\Collection         $data
     * @param \Closure|null                                $condition
     *
     * @return array|\Sprocketbox\Articulate\Support\Collection
     */
    public function get(Repository $repository, string $attribute, $data = [], ?\Closure $condition = null);

    /**
     * Check for the presence of the entity.
     *
     * @param mixed                                           $builder
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $localMapping
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $foreignMapping
     *
     * @return mixed
     */
    public function has($builder, EntityMapping $localMapping, EntityMapping $foreignMapping);

    /**
     * Whether or not persists should cascade.
     *
     * @return bool
     */
    public function shouldCascade(): bool;

    /**
     * Get the local key for persisting.
     *
     * @return null|string
     */
    public function getLocalKey(): ?string;
}