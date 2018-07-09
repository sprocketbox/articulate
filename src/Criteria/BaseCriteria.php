<?php

namespace Ollieread\Articulate\Criteria;

use Ollieread\Articulate\Contracts\Criteria;

abstract class BaseCriteria implements Criteria
{
    /**
     * @var int
     */
    protected $priority;

    /**
     * @var array
     */
    protected $validEntities = [];

    public function setPriority(int $priority = 0): self
    {
        $this->priority = $priority;
        return $this;
    }

    protected function setValidEntities(array $validEntities = []): self
    {
        $this->validEntities = $validEntities;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public abstract function perform($query);

    /**
     * @param string $entityClass
     *
     * @return bool
     */
    public function validFor(string $entityClass): bool
    {
        return ! $this->validEntities ? true : \in_array($entityClass, $this->validEntities, true);
    }
}