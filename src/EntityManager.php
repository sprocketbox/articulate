<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Entities\BaseEntity;

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

    public function register(EntityMapping $mapping)
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
    }

    public function getMapping(string $entity): ?Mapping
    {
        return $this->mappings->get($entity, null);
    }

    public function repository(string $entity)
    {
        $mapper = $this->getMapping($entity);
        $repository = $mapper->getRepository();

        if (class_exists($repository)) {
            return new $repository(app('db'), $this, $mapper);
        }

        // Throw an exception
    }

    public function hydrate(string $entityClass, array $attributes = []): ?BaseEntity
    {
        $mapper = $this->getMapping($entityClass);

        if ($mapper) {
            $entity = new $entityClass;

            foreach ($attributes as $key => $value) {
                $setter = 'set' . studly_case($key);

                if (method_exists($entity, $setter)) {
                    $column = $mapper->getColumn($key);
                    $entity->{$setter}($column->cast($value));
                }
            }

            $entity->clean();

            return $entity;
        }
    }
}