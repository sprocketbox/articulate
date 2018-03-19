<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Entities\BaseEntity;
use Ollieread\Articulate\Repositories\EntityRepository;

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
     * @param \Ollieread\Articulate\EntityMapping $mapping
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
        $mapper = new Mapping($entity, $connection ?? config('database.default'), $table);
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
     * @return \Ollieread\Articulate\Mapping
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
     * @return null|\Ollieread\Articulate\Repositories\EntityRepository
     * @throws \RuntimeException
     */
    public function repository(string $entity): ?EntityRepository
    {
        $mapper     = $this->getMapping($entity);
        $repository = $mapper->getRepository() ?? EntityRepository::class;

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
     * @return null|\Ollieread\Articulate\Entities\BaseEntity
     * @throws \RuntimeException
     */
    public function hydrate(string $entityClass, $attributes = []): ?BaseEntity
    {
        $attributes = (array) $attributes;
        $mapper     = $this->getMapping($entityClass);
        /**
         * @var BaseEntity $entity
         */
        $entity = new $entityClass;

        foreach ($attributes as $key => $value) {
            $setter = 'set' . studly_case($key);

            if (method_exists($entity, $setter)) {
                $column = $mapper->getColumn($key);

                if ($column) {
                    $entity->{$setter}($column->cast($value));
                }
            }
        }

        $entity->clean();

        return $entity;
    }
}