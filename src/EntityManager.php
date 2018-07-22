<?php

namespace Sprocketbox\Articulate;

use Sprocketbox\Articulate\Attributes\ComponentAttribute;
use Sprocketbox\Articulate\Contracts\Attributeable;
use Sprocketbox\Articulate\Contracts\ComponentMapping;
use Sprocketbox\Articulate\Contracts\ComponentMapper;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\Contracts\Repository as RepositoryContract;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\Entities\EntityMapper;
use Illuminate\Support\Collection as LaravelCollection;
use Sprocketbox\Articulate\Contracts\Attribute;
use Sprocketbox\Articulate\Contracts\EntityMapping as MappingContract;
use Sprocketbox\Articulate\Repositories\Repository;

/**
 * Class EntityManager
 *
 * @mixin Concerns\HandlesMappings
 * @mixin Concerns\HandlesSources
 *
 * @package Sprocketbox\Articulate
 */
class EntityManager
{
    use Concerns\HandlesMappings,
        Concerns\HandlesSources;

    public function __construct()
    {
        $this->entityMappings    = new LaravelCollection;
        $this->componentMappings = new LaravelCollection;
        $this->sources           = new LaravelCollection;
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\EntityMapper $mapper
     */
    public function registerEntity(EntityMapper $mapper): void
    {
        $entity = $mapper->entity();

        if ($this->hasEntityMapping($entity)) {
            throw new \RuntimeException(sprintf('Entity %s already registered', $entity));
        }

        $source = $this->getSource($mapper->source());

        if (! $source) {
            throw new \RuntimeException(sprintf('Invalid source %s for entity %s', $source, $entity));
        }

        $mapping = $source->newMapping($entity, $mapper->source());
        $mapper->map($mapping);

        $this->registerEntityMapping($entity, $mapping);

        $repository = $mapping->getRepository();

        // If there's a repository, and it exists
        if ($repository && class_exists($repository)) {
            // Map it for the binding
            app()->bind($repository, function () use ($entity): RepositoryContract {
                return $this->repository($entity);
            });
        }
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\ComponentMapper $mapper
     */
    public function registerComponent(ComponentMapper $mapper): void
    {
        $component = $mapper->component();

        if ($this->hasComponentMapping($component)) {
            throw new \RuntimeException(sprintf('Component %s already registered', $component));
        }

        $mapping = app()->makeWith(ComponentMapping::class, [$component]);
        $mapper->map($mapping);

        $this->registerComponentMapping($component, $mapping);
    }

    /**
     * @param string $entity
     *
     * @return MappingContract
     */
    public function mapping(string $entity): MappingContract
    {
        if (! $this->hasEntityMapping($entity)) {
            throw new \InvalidArgumentException(sprintf('Mapping not found for entity %s', $entity));
        }

        return $this->getEntityMapping($entity);
    }

    /**
     * @param string $entity
     *
     * @return null|\Sprocketbox\Articulate\Contracts\Repository
     * @throws \InvalidArgumentException
     */
    public function repository(string $entity): ?RepositoryContract
    {
        $mapping    = $this->mapping($entity);
        $source     = $this->getSource($mapping->getSource());
        $repository = $mapping->getRepository() ?? Repository::class;

        if (class_exists($repository)) {
            return new $repository($this, $mapping, $source);
        }

        return null;
    }

    /**
     * @param string $entity
     *
     * @return string
     */
    public function key(string $entity): string
    {
        $mapping = $this->mapping($entity);

        return $mapping->getKey();
    }

    /** @noinspection ArrayTypeOfParameterByDefaultValueInspection */

    /**
     * @param string $class
     * @param array  $data
     *
     * @param bool   $persisted
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Components\Component
     * @throws \InvalidArgumentException
     */
    public function hydrate(string $class, $data = [], bool $persisted = true, $mapping = null)
    {
        if (! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Invalid class provided %s', $class));
        }

        if ($data instanceof \stdClass) {
            $data = (array)$data;
        }

        if ($data instanceof Entity) {
            // This is being thrown here instead of simply returning, because it points to something
            // not quite working as it should, and we should address this wound instead of letting it fester
            throw new \InvalidArgumentException('Already hydrated');
        }

        if (empty($data)) {
            throw new \InvalidArgumentException('No attributes provided for hydration');
        }

        if ($data instanceof LaravelCollection) {
            // Since a collection is essentially just a fancy array, we're going to skip the rest of this method,
            // and pass in an arrayed version of the collection to itself. If it still doesn't work, it'll be
            // caught by the other error clauses above
            return $this->hydrate($class, $data->toArray(), $persisted);
        }

        $mapping = $mapping ?? $this->getEntityMapping($class) ?? $this->getComponentMapping($class);

        if (! $mapping) {
            throw new \InvalidArgumentException(sprintf('Invalid attributeable class %s', $class));
        }

        $attributes = $mapping->getAttributes();

        if ($mapping instanceof EntityMapping) {
            $entity        = true;
            $attributeable = $mapping->make($data);
            $class         = \get_class($attributeable);

            if ($mapping->hasMultipleInheritance()) {
                $attributes = $attributes->filter(function (Attribute $attribute) use ($class) {
                    return $attribute->belongsTo($class);
                });
            }
        } else {
            $entity        = false;
            $attributeable = $mapping->make();
        }

        $attributeable::hydrating($attributeable, $data);

        // Noe we want to cycle through and populate any components
        $attributes->filter(function (Attribute $attribute) {
            return $attribute->isComponent();
        })->each(function (ComponentAttribute $attribute) use (&$data, $attributeable, $persisted) {
            $componentClass = $attribute->getComponent();

            if ($componentClass) {
                $mapping = $attribute->getCustomMapping() ?? $this->getComponentMapping($componentClass);

                if ($mapping) {
                    $attributeNames = $mapping->getAttributes()->map(function (Attribute $attribute) {
                        return $attribute->getColumnName() ?? $attribute->getName();
                    })->toArray();

                    $componentData = array_only($data, $attributeNames);

                    if ($componentData) {
                        $attributeable->set($attribute->getName(), $this->hydrate($componentClass, $componentData, $persisted, $mapping));
                        // We're going to remove the component attributes from the dataset
                        $data = array_except($data, $attributeNames);
                    } else {
                        $attributeable->set($attribute->getName(), new $componentClass);
                    }
                }
            }
        });

        // Cycle through the data and populate the attributes
        collect($data)->each(function ($value, $key) use ($attributes, $attributeable, $data) {
            $attribute = $attributes->first(function (Attribute $attribute) use ($key) {
                return $attribute->getName() === $key || $attribute->getColumnName() === $key;
            });

            if ($attribute) {
                $attributeName = $attribute->getName();
                $columnName    = $attribute->getColumnName();

                // If a mapping has a different column name, we want to actually set that attribute
                // simply because it's useful to have that data
                if ($attributeName && $columnName !== $attributeName) {
                    $attributeable->set($columnName, $value);
                }

                $attributeable->set($attributeName, $attribute->cast($value, $data));
            }
        });

        $attributeable::hydrated($attributeable, $data);

        // Now that we're all done, we'll clean the entity so that it doesn't appear to be dirty
        $attributeable->clean();

        if ($persisted && $entity) {
            // We can assume that the entity has been 'persisted', which means that it exists in the datasource
            // so we set that flag here
            $attributeable->setPersisted();
        }

        return $attributeable;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Attributeable $attributeable
     * @param \Closure|null                                   $filter
     *
     * @return array
     */
    public function dehydrate(Attributeable $attributeable, \Closure $filter = null): array
    {
        $mapping         = $this->getEntityMapping(\get_class($attributeable)) ?? $this->getComponentMapping(\get_class($attributeable));
        $dehydratedArray = [];

        if ($mapping) {
            $attributes = collect($attributeable->getAll());

            if ($filter) {
                $attributes = $attributes->filter($filter);
            }

            $dehydratedArray = $attributes->mapWithKeys(function ($value, $key) use ($mapping) {
                $attribute = $mapping->getAttribute($key);

                if ($attribute) {
                    if ($attribute->isComponent()) {
                        return $this->dehydrate($value);
                    }

                    return [$key => $attribute->parse($value)];
                }

                return [$key => $value];
            })->toArray();
        }

        return $dehydratedArray;
    }
}