<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Contracts\Entity;
use Ollieread\Articulate\Contracts\EntityMapping;
use Ollieread\Articulate\Contracts\EntityRepository;
use Ollieread\Articulate\Contracts\Mapping;
use Ollieread\Articulate\Repositories\EntityRepository as BaseRepository;

class EntityManager
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $mappings;

    public function __construct()
    {
        $this->mappings = new Collection;
    }

    /**
     * @param \Ollieread\Articulate\Contracts\EntityMapping $mapping
     *
     * @throws \RuntimeException
     */
    public function register(EntityMapping $mapping): void
    {
        $entity     = $mapping->entity();
        $table      = $mapping->table();
        $connection = $mapping->connection();

        if ($this->mappings->has($entity)) {
            throw new \RuntimeException('Entity already registered');
        }

        // Create mapper
        $mapper = app()->makeWith(Mapping::class, [$entity, $connection ?? config('database.default'), $table]);
        $mapping->map($mapper);

        // Add the entity mapping
        $this->mappings->put($entity, $mapper);

        $repository = $mapper->getRepository();

        // If there's a repository, and it exists
        if ($repository && class_exists($repository)) {
            // Map it for the binding
            app()->bind($repository, function () use ($entity): EntityRepository {
                return $this->repository($entity);
            });
        }
    }

    /**
     * @param string $entity
     *
     * @return \Ollieread\Articulate\Contracts\Mapping
     * @throws \RuntimeException
     */
    public function getMapping(string $entity): Mapping
    {
        if (! $this->mappings->has($entity)) {
            throw new \RuntimeException('Mapping not found for: ' . $entity);
        }

        return $this->mappings->get($entity);
    }

    /**
     * @param string $entity
     *
     * @return null|\Ollieread\Articulate\Contracts\EntityRepository
     * @throws \RuntimeException
     */
    public function repository(string $entity): ?EntityRepository
    {
        $mapper     = $this->getMapping($entity);
        $repository = $mapper->getRepository() ?? BaseRepository::class;

        if (class_exists($repository)) {
            return new $repository($this, $mapper);
        }

        return null;
    }

    /** @noinspection ArrayTypeOfParameterByDefaultValueInspection */

    /**
     * @param string $entityClass
     * @param array  $attributes
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity
     * @throws \RuntimeException
     */
    public function hydrate(string $entityClass, $attributes = []): ?Entity
    {
        if ($attributes instanceof Entity) {
            dd(debug_backtrace());
            throw new \RuntimeException('Can\'t hydrate an entity from an entity');
        }

        if (empty($attributes)) {
            throw new \RuntimeException('No attributes provided for entity hydration');
        }

        if ($attributes instanceof Collection) {
            throw new \RuntimeException('Can\'t hydrate a collection');
        }

        //$attributes = (array) $attributes;
        $mapper     = $this->getMapping($entityClass);
        /**
         * @var \Ollieread\Articulate\Contracts\Entity $entity
         */
        $entity = new $entityClass;

        $attributes = $mapper->getColumns()->map(function (Column $column) {
            return $column->getDefault();
        })->merge($attributes);

        foreach ($attributes as $key => $value) {
            $setter = 'set' . studly_case($key);

            if (method_exists($entity, $setter)) {
                $column = $mapper->getColumn($key);

                if ($column) {
                    $value = $column->cast($value);
                }

                $entity->{$setter}($value);
            }
        }

        $entity->clean();

        return $entity;
    }
}