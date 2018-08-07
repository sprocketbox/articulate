<?php

namespace Sprocketbox\Articulate\Contracts;

interface Resolver
{
    /**
     * @param \Sprocketbox\Articulate\Contracts\Repository $repository
     * @param array                                        $data
     * @param \Closure|null                                $condition
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     */
    public function get(Repository $repository, array $data = [], ?\Closure $condition = null);

    /**
     * @param mixed                                           $builder
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $localMapping
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $foreignMapping
     *
     * @return mixed
     */
    public function has($builder, EntityMapping $localMapping, EntityMapping $foreignMapping);
}