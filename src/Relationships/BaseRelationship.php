<?php

namespace Ollieread\Articulate\Relationships;

abstract class BaseRelationship
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $relationshipClass;

    /**
     * @var string
     */
    protected $entityClass;

    public function __construct(string $name, string $relationshipClass, string $entityClass)
    {
        $this->name              = $name;
        $this->relationshipClass = $relationshipClass;
        $this->entityClass       = $entityClass;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRelationshipClass(): string
    {
        return $this->relationshipClass;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}