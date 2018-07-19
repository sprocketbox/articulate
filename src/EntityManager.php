<?php

namespace Sprocketbox\Articulate;

use Sprocketbox\Articulate\Components\Component;
use Sprocketbox\Articulate\Contracts\ComponentMapping;
use Sprocketbox\Articulate\Contracts\ComponentMapper;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\Contracts\Repository as RepositoryContract;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\Entities\EntityMapper;
use Sprocketbox\Articulate\Event\Hydrated;
use Sprocketbox\Articulate\Event\Hydrating;
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

    /** @noinspection ArrayTypeOfParameterByDefaultValueInspection */

    /**
     * @param string $class
     * @param array  $attributes
     *
     * @param bool   $persisted
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Components\Component
     * @throws \InvalidArgumentException
     */
    public function hydrate(string $class, $attributes = [], bool $persisted = true)
    {
        if (! class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Invalid class provided %s', $class));
        }

        if ($attributes instanceof \stdClass) {
            $attributes = (array)$attributes;
        }

        if ($attributes instanceof Entity) {
            // This is being thrown here instead of simply returning, because it points to something
            // not quite working as it should, and we should address this wound instead of letting it fester
            throw new \InvalidArgumentException('Already hydrated');
        }

        if (empty($attributes)) {
            throw new \InvalidArgumentException('No attributes provided for hydration');
        }

        if ($attributes instanceof LaravelCollection) {
            // Since a collection is essentially just a fancy array, we're going to skip the rest of this method,
            // and pass in an arrayed version of the collection to itself. If it still doesn't work, it'll be
            // caught by the other error clauses above
            return $this->hydrate($class, $attributes->toArray(), $persisted);
        }

        $hydratable = app()->make($class);

        if ($hydratable instanceof Entity) {
            $mapping = $this->getEntityMapping($class);

            if (! $mapping) {
                throw new \InvalidArgumentException(sprintf('Invalid entity %s', $class));
            }

            return $this->hydrateEntity($hydratable, $mapping, $attributes, $persisted);
        }

        if ($hydratable instanceof Component) {
            $mapping = $this->getComponentMapping($class);

            if (! $mapping) {
                throw new \InvalidArgumentException(sprintf('Invalid component %s', $class));
            }

            return $this->hydrateComponent($hydratable, $mapping, $attributes);
        }
    }

    private function hydrateEntity(Entity $entity, EntityMapping $mapping, array $attributes = [], bool $persisted = true): Entity
    {
        $entityAttributes    = $mapping->getAttributes();
        $componentAttributes = $entityAttributes
            ->filter(function (Attribute $attribute) {
                return $attribute->isComponent();
            });

        // Now lets fire the pre hydration event for ...reasons
        Hydrating::dispatch($entity, $attributes);

        $componentAttributes
            ->each(function (Attribute $attribute) use (&$attributes, $entity) {
                $componentClass = $attribute->getComponent();

                if ($componentClass) {
                    $mapping = $this->getComponentMapping($componentClass);

                    if ($mapping) {
                        $attributeNames = $mapping->getAttributes()->map(function (Attribute $attribute) {
                            return $attribute->getColumnName() ?? $attribute->getName();
                        });
                        $entity->set($attribute->getName(), $this->hydrateComponent(new $componentClass($entity), $mapping, array_only($attributes, $attributeNames)));
                        $attributes = array_except($attributes, $attributeNames);
                    }
                }
            });

        collect($attributes)
            ->each(function ($value, $key) use ($entityAttributes, $entity) {
                $attribute = $entityAttributes->first(function (Attribute $attribute) use($key) {
                    return $attribute->getName() === $key || $attribute->getColumnName() === $key;
                });

                if ($attribute) {
                    $attributeName = $attribute->getName();
                    $columnName    = $attribute->getColumnName();

                    // If a mapping has a different column name, we want to actually set that attribute
                    // simply because it's useful to have that data
                    if ($attributeName && $columnName !== $attributeName) {
                        $entity->set($columnName, $value);
                    }

                    $value = $attribute->cast($value);
                }

                $entity->set($key, $value);
            });

        // Now lets fire the post hydration event for ...reasons
        Hydrated::dispatch($entity, $attributes);

        // Now that we're all done, we'll clean the entity so that it doesn't appear to be dirty
        $entity->clean();

        if ($persisted) {
            // We can assume that the entity has been 'persisted', which means that it exists in the datasource
            // so we set that flag here
            $entity->setPersisted();
        }

        return $entity;
    }

    public function hydrateComponent(Component $component, ComponentMapping $mapping, $attributes = []): Component
    {
        $componentAttributes = $mapping->getAttributes();

        collect($attributes)
            ->each(function ($value, $key) use ($componentAttributes, $component) {
                $attribute = $componentAttributes->first(function (Attribute $attribute) use($key) {
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

                    $component->set($attributeName, $value);
                }
            });

        return $component;
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Components\Component $hydrated
     * @param \Closure|null                                                                        $filter
     *
     * @return array
     */
    public function dehydrate($hydrated, \Closure $filter = null): array
    {
        if ($hydrated instanceof Entity) {
            $mapping = $this->getEntityMapping(\get_class($hydrated));
        } else {
            $mapping = $this->getComponentMapping(\get_class($hydrated));
        }

        $dehydratedArray = [];

        if ($mapping) {
            $attributes = collect($hydrated->getAll());

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