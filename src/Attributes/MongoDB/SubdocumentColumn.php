<?php

namespace Sprocketbox\Articulate\Attributes\MongoDB;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Attributes\BaseAttribute;
use Sprocketbox\Articulate\Contracts\Entity;
use Sprocketbox\Articulate\EntityManager;

class SubdocumentColumn extends BaseAttribute
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
     * @param string $attributeName
     * @param string $componentClass
     * @param bool   $multiple
     */
    public function __construct(string $attributeName, string $componentClass, bool $multiple = false)
    {
        parent::__construct($attributeName);
        $this->entityClass = $componentClass;
        $this->multiple    = $multiple;
    }

    /**
     * @param       $value
     * @param array $data
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection|null
     */
    public function cast($value, array $data = [])
    {
        if (! $value || $value instanceof $this->entityClass) {
            return $value;
        }

        if (\is_array($value) && (\is_array(array_first($value)) || array_first($value) instanceof \stdClass)) {
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
     * @param       $value
     * @param array $data
     *
     * @return array|null
     */
    public function parse($value, array $data = []): array
    {
        if ($this->multiple && $value instanceof Collection) {
            return $value->map(function ($item) {
                return $this->parse($item);
            })->toArray();
        }

        if ($value instanceof Entity) {
            return app(EntityManager::class)->dehydrate($value);
        }

        return (array) $value;
    }
}