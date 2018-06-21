<?php

namespace Ollieread\Articulate\Columns\MongoDB;

use Ollieread\Articulate\Support\Collection;
use Ollieread\Articulate\Columns\BaseColumn;
use Ollieread\Articulate\Contracts\Entity;
use Ollieread\Articulate\EntityManager;

class SubdocumentColumn extends BaseColumn
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
        $this->multiple    = $multiple;
    }

    /**
     * @param $value
     *
     * @return null|\Ollieread\Articulate\Entities\BaseEntity|\Ollieread\Articulate\Support\Collection|null
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
     * @return array|null
     * @throws \RuntimeException
     */
    public function toDatabase($value): array
    {
        if ($this->multiple && $value instanceof Collection) {
            return $value->map(function ($item) {
                return $this->toDatabase($item);
            })->toArray();
        }

        if ($value instanceof Entity) {
            return app(EntityManager::class)->dehydrate($value);
        }

        return (array) $value;
    }
}