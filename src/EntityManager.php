<?php

namespace Sprocketbox\Articulate;

use Illuminate\Support\Collection as LaravelCollection;
use Sprocketbox\Articulate\Contracts\ComponentMapper;
use Sprocketbox\Articulate\Contracts\EntityMapping as MappingContract;
use Sprocketbox\Articulate\Contracts\Repository as RepositoryContract;
use Sprocketbox\Articulate\Contracts\Source;
use Sprocketbox\Articulate\Entities\EntityMapper;
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
        Concerns\HandlesSources,
        Concerns\HandlesAttributeables;

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

        if (! $source || ! ($source instanceof Source)) {
            throw new \RuntimeException(sprintf('Invalid source %s for entity %s', $mapper->source(), $entity));
        }

        $mapping = $source->newMapping($entity);
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

        $mapping = new Components\ComponentMapping($component);
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
}