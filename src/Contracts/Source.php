<?php

namespace Sprocketbox\Articulate\Contracts;

interface Source
{
    /**
     * @param string $entity
     * @param string $source
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function newMapping(string $entity, string $source);

    public function builder(...$arguments);
}