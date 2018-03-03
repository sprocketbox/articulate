<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;

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
            return new $repository(app('db'), $mapper);
        }

        // Throw an exception
    }
}