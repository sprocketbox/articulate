<?php

namespace Ollieread\Articulate\Columns;

use Illuminate\Support\Collection;
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
     * @var bool
     */
    protected $multiple;

    /**
     * EntityColumn constructor.
     *
     * @param string $columnName
     * @param string $entityClass
     * @param bool   $multiple
     */
    public function __construct(string $columnName, string $entityClass, bool $multiple = false)
    {
        parent::__construct($columnName);
        $this->entityClass = $entityClass;
        $this->multiple = $multiple;
    }

    /**
     * @param $value
     *
     * @return null|\Ollieread\Articulate\Entities\BaseEntity|\Illuminate\Support\Collection|null
     * @throws \RuntimeException
     */
    public function cast($value)
    {
        if (! $value || $value instanceof $this->entityClass) {
            return $value;
        }

        if (\is_array($value) && \is_array(array_first($value))) {
            $value = collect($value);
        }

        if ($this->multiple && $value instanceof Collection) {
            return $value->map(function ($entity) {
                return $this->cast($entity);
            });
        }

        return app(EntityManager::class)->hydrate($this->entityClass, $value);
    }

    /**
     * @param $value
     *
     * @return string|null
     * @throws \RuntimeException
     */
    public function toDatabase($value): ?string
    {
        if ($this->multiple) {
            return null;
        }

        $mapping = app(EntityManager::class)->getMapping($this->entityClass);

        return $value instanceof BaseEntity ? $value->{'get' . studly_case($mapping->getKey())}() : $value;
    }
}