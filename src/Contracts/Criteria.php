<?php

namespace Ollieread\Articulate\Contracts;

interface Criteria
{
    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param $query
     *
     * @return mixed
     */
    public function perform($query);

    /**
     * @param string $entityClass
     *
     * @return bool
     */
    public function validFor(string $entityClass): bool;
}