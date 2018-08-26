<?php

namespace Sprocketbox\Articulate\Concerns;

use Illuminate\Support\Collection as LaravelCollection;
use Sprocketbox\Articulate\Attributes\ComponentAttribute;
use Sprocketbox\Articulate\Components\Component;
use Sprocketbox\Articulate\Contracts\Attribute;
use Sprocketbox\Articulate\Contracts\Attributeable;
use Sprocketbox\Articulate\Contracts\ComponentMapping;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\Support\Collection;

trait HandlesAttributeables
{

    /** @noinspection ArrayTypeOfParameterByDefaultValueInspection */

    /**
     * @param string                  $class
     * @param LaravelCollection|array $data
     * @param bool                    $persisted
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     */
    public function hydrate(string $class, $data = [], bool $persisted = true)
    {
        return $this->hydrateEntity($class, $data, $persisted);
    }

    /**
     * @param string $class
     * @param array  $data
     * @param bool   $persisted
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     */
    public function hydrateEntity(string $class, $data = [], bool $persisted = false)
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('No attributes provided for hydration');
        }

        $mapping    = $this->getEntityMapping($class);
        $attributes = $mapping->getAttributes();
        $attributes->filter(function (Attribute $attribute) {
            return $attribute->isComponent();
        })->each(function (ComponentAttribute $attribute, string $name) use (&$data) {
            $mapping = $attribute->getCustomMapping() ?? $this->getComponentMapping($attribute->getComponent());

            if ($mapping) {
                $componentAttributes = $mapping->getAttributes()->keys();
                $componentData       = array_only($data, $componentAttributes);
                $data                = array_except($data, $componentAttributes);
                $data[$name]         = $componentData;
            }
        });

        if ($data instanceof LaravelCollection) {
            return new Collection($data->map(function ($row) use ($mapping, $attributes, $persisted) {
                $entity = $this->populateEntity($mapping, $attributes, (array)$row);

                if ($persisted) {
                    $entity->setPersisted();
                }

                return $entity;
            })->toArray());
        }

        $entity = $this->populateEntity($mapping, $attributes, (array)$data);

        if ($persisted) {
            $entity->setPersisted();
        }

        return $entity;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\EntityMapping $mapping
     * @param \Illuminate\Support\Collection                  $attributes
     * @param array                                           $data
     *
     * @return \Sprocketbox\Articulate\Entities\Entity
     */
    protected function populateEntity(EntityMapping $mapping, LaravelCollection $attributes, array $data)
    {
        $entity = $mapping->make($data);

        if ($mapping->hasMultipleInheritance()) {
            $class      = \get_class($entity);
            $attributes = $attributes->filter(function (Attribute $attribute) use ($class) {
                return $attribute->belongsTo($class);
            });
        }

        $entity::hydrating($entity, $data);

        collect($data)->each(function ($value, $key) use ($attributes, $entity, $data) {
            $attribute = $attributes->first(function (Attribute $attribute) use ($key) {
                return $attribute->getName() === $key || $attribute->getColumnName() === $key;
            });

            if ($attribute) {
                $attributeName = $attribute->getName();
                $columnName    = $attribute->getColumnName();

                // If a mapping has a different column name, we want to actually set that attribute
                // simply because it's useful to have that data
                if ($attributeName && $columnName !== $attributeName) {
                    $entity->set($columnName, $attribute->parse($value, $data));
                }

                $entity->set($attributeName, $attribute->cast($value, $data));
            }
        });

        $entity::hydrated($entity, $data);

        $entity->clean();

        return $entity;
    }

    /**
     * @param string                                                  $class
     * @param null|\Sprocketbox\Articulate\Contracts\ComponentMapping $mapping
     * @param array                                                   $data
     *
     * @return \Sprocketbox\Articulate\Components\Component
     */
    public function hydrateComponent(string $class, ?ComponentMapping $mapping = null, $data = [])
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('No attributes provided for hydration');
        }

        $mapping    = $mapping ?? $this->getComponentMapping($class);
        $attributes = $mapping->getAttributes();
        $component  = $mapping->make($data);

        $component::hydrating($component, $data);

        collect($data)->each(function ($value, $key) use ($attributes, $component, $data) {
            $attribute = $attributes->first(function (Attribute $attribute) use ($key) {
                return $attribute->getName() === $key || $attribute->getColumnName() === $key;
            });

            if ($attribute) {
                $attributeName = $attribute->getName();
                $columnName    = $attribute->getColumnName();

                // If a mapping has a different column name, we want to actually set that attribute
                // simply because it's useful to have that data
                if ($attributeName && $columnName !== $attributeName) {
                    $component->set($columnName, $value);
                }

                $component->set($attributeName, $attribute->cast($value, $data));
            }
        });

        $component::hydrated($component, $data);

        return $component;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Attributeable $attributeable
     * @param \Closure|null                                   $filter
     *
     * @return array
     */
    public function dehydrate(Attributeable $attributeable, \Closure $filter = null): array
    {
        if ($attributeable instanceof Entity) {
            return $this->dehydrateEntity($attributeable, $filter);
        }

        if ($attributeable instanceof Component) {
            return $this->dehydrateComponent($attributeable);
        }

        throw new \InvalidArgumentException('Invalid attributable object');
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     * @param \Closure|null                           $filter
     *
     * @return array
     */
    public function dehydrateEntity(Entity $entity, \Closure $filter = null): array
    {
        $class   = \get_class($entity);
        $mapping = $this->getEntityMapping($class);

        $attributes = $mapping->getAttributes()->map(function (Attribute $attribute) {
            return $attribute->getDefault();
        })->merge(collect($entity->getAll()))->each(function ($value, $key) use($entity) {
            $entity->set($key, $value);
        });

        if ($filter) {
            $attributes = $attributes->filter($filter);
        }

        return $attributes->mapWithKeys(function ($value, $key) use ($mapping) {
            $attribute = $mapping->getAttribute($key);

            if ($attribute) {
                if ($attribute->isComponent()) {
                    return $attribute->parse($value);
                }

                $columnName = $attribute->getColumnName();

                if ($columnName !== $key) {
                    $key = $columnName;
                }

                return [$key => $attribute->parse($value)];
            }

            return [$key => $value];
        })->toArray();
    }

    /**
     * @param \Sprocketbox\Articulate\Components\Component $component
     *
     * @return array
     */
    public function dehydrateComponent(Component $component): array
    {
        $class   = \get_class($component);
        $mapping = $this->getComponentMapping($class);

        $attributes = $mapping->getAttributes()->map(function (Attribute $attribute) {
            return $attribute->getDefault();
        })->merge(collect($component->getAll()));

        return $attributes->mapWithKeys(function ($value, $key) use ($mapping) {
            $attribute = $mapping->getAttribute($key);

            if ($attribute) {
                $columnName = $attribute->getColumnName();

                if ($columnName !== $key) {
                    $key = $columnName;
                }

                return [$key => $attribute->parse($value)];
            }

            return [$key => $value];
        })->toArray();
    }
}