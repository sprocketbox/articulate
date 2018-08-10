<?php

namespace Sprocketbox\Articulate\Contracts;

interface Resolver
{
    /**
     * @param \Sprocketbox\Articulate\Contracts\Repository $repository
     * @param string                                       $attribute
     * @param array|\Illuminate\Support\Collection         $data
     * @param \Closure|null                                $condition
     *
     * @return array|\Sprocketbox\Articulate\Support\Collection
     */
    public function get(Repository $repository, string $attribute, $data = [], ?\Closure $condition = null);

    /**
     * @param mixed                                           $builder
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $localMapping
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $foreignMapping
     *
     * @return mixed
     */
    public function has($builder, EntityMapping $localMapping, EntityMapping $foreignMapping);
}