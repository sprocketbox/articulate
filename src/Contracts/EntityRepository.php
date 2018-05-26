<?php

namespace Ollieread\Articulate\Contracts;

use Ollieread\Articulate\EntityManager;

/**
 * Class EntityRepository
 *
 * @package Ollieread\Articulate\Repositories
 */
interface EntityRepository
{
    /**
     * @return \Ollieread\Articulate\EntityManager
     */
    public function manager(): EntityManager;

    /**
     * @return \Ollieread\Articulate\Contracts\Mapping
     */
    public function mapping(): Mapping;

    /**
     * @return string
     */
    public function entity(): string;

    /**
     * @param $result
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity|static|\Illuminate\Support\Collection
     * @throws \RuntimeException
     */
    public function hydrate($result);

    /**
     * @param mixed $identifier
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity
     */
    public function load($identifier);
}