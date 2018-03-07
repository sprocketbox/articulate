<?php

namespace Ollieread\Articulate\Relationships;

class BelongsTo extends BaseRelationship
{

    /**
     * @var string
     */
    protected $foreignKey;

    public function __construct(string $name, string $relationshipClass, string $entityClass, string $foreignKey)
    {
        parent::__construct($name, $relationshipClass, $entityClass);
        $this->foreignKey = $foreignKey;
    }

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }
}