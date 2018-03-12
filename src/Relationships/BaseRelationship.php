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

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    public function __construct(string $name, string $relationshipClass, string $entityClass, string $foreignKey, ?string $localKey = 'id')
    {
        $this->name              = $name;
        $this->relationshipClass = $relationshipClass;
        $this->entityClass       = $entityClass;
        $this->foreignKey        = $foreignKey;
        $this->localKey          = $localKey;
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

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    public abstract function load(&$results, array $children = []);
}