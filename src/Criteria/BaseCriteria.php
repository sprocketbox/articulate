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

    public function __construct(int $priority = 0, array $validEntities = [])
    {
        $this->priority      = $priority;
        $this->validEntities = $validEntities;
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