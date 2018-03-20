<?php

namespace Ollieread\Articulate\Contracts;

use Ollieread\Articulate\EntityManager;
use Ollieread\Articulate\Mapping;

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
     * @return \Ollieread\Articulate\Mapping
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
}