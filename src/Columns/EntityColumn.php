<?php

namespace Ollieread\Articulate\Columns;

use Ollieread\Articulate\Entities\BaseEntity;
use Ollieread\Articulate\EntityManager;

class EntityColumn extends BaseColumn
{
    /**
     * @var string
     */
    protected $entityClass;

    public function __construct(string $columnName, string $entityClass)
    {
        parent::__construct($columnName);
        $this->entityClass = $entityClass;
    }

    public function cast($value)
    {
        return app(EntityManager::class)->hydrate($value);
    }

    public function toDatabase($value)
    {
        $mapping = app(EntityManager::class)->getMapping($this->entityClass);

        return $value instanceof BaseEntity ? $value->{'get' . studly_case($mapping->getKey())}() : null;
    }
}