<?php

namespace Sprocketbox\Articulate\Contracts;

interface Source
{
    /**
     * @param string $entity
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function newMapping(string $entity);

    public function builder(...$arguments);

    public function name(): string;
}