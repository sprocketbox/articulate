<?php

namespace Ollieread\Articulate\Relationships;

class BelongsTo
{
    /**
     * @var string
     */
    protected $childEntity;

    /**
     * @var string
     */
    protected $parentEntity;

    /**
     * @var string
     */
    protected $foreignKey;

    public function __construct(string $childEntity, string $parentEntity, string $foreignKey)
    {
        $this->childEntity  = $childEntity;
        $this->parentEntity = $parentEntity;
        $this->foreignKey   = $foreignKey;
    }

    /**
     * @return string
     */
    public function getChildEntity(): string
    {
        return $this->childEntity;
    }

    /**
     * @return string
     */
    public function getParentEntity(): string
    {
        return $this->parentEntity;
    }

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }
}