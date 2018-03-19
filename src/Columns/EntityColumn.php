<?php

namespace Ollieread\Articulate\Columns;

use Ollieread\Articulate\Entities\BaseEntity;
use Ollieread\Articulate\EntityManager;

/**
 * Class EntityColumn
 *
 * @package Ollieread\Articulate\Columns
 */
class EntityColumn extends BaseColumn
{
    /**
     * @var string
     */
    protected $entityClass;

    /**
     * EntityColumn constructor.
     *
     * @param string $columnName
     * @param string $entityClass
     */
    public function __construct(string $columnName, string $entityClass)
    {
        parent::__construct($columnName);
        $this->entityClass = $entityClass;
    }

    /**
     * @param $value
     *
     * @return null|\Ollieread\Articulate\Entities\BaseEntity
     * @throws \RuntimeException
     */
    public function cast($value): ?BaseEntity
    {
        return app(EntityManager::class)->hydrate($value);
    }

    /**
     * @param $value
     *
     * @return string|null
     * @throws \RuntimeException
     */
    public function toDatabase($value): ?string
    {
        $mapping = app(EntityManager::class)->getMapping($this->entityClass);

        return $value instanceof BaseEntity ? $value->{'get' . studly_case($mapping->getKey())}() : null;
    }
}