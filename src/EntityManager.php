<?php

namespace Ollieread\Articulate;

use Illuminate\Database\Concerns\BuildsQueries;
use Ollieread\Articulate\Support\Collection;
use Illuminate\Support\Collection as LaravelCollection;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Contracts\Entity;
use Ollieread\Articulate\Contracts\EntityMapping;
use Ollieread\Articulate\Contracts\EntityRepository;
use Ollieread\Articulate\Contracts\Mapping;
use Ollieread\Articulate\Repositories\EntityRepository as BaseRepository;

class EntityManager
{
    use BuildsQueries;

    /**
     * @var LaravelCollection
     */
    protected $mappings;

    public function __construct()
    {
        $this->mappings = new LaravelCollection;
    }

    /**
     * @param \Ollieread\Articulate\Contracts\EntityMapping $mapping
     *
     * @throws \InvalidArgumentException
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
     * @throws \InvalidArgumentException
     */
    public function getMapping(string $entity): Mapping
    {
        if (! $this->mappings->has($entity)) {
            throw new \InvalidArgumentException(sprintf('Mapping not found for entity: %s', $entity));
        }

        return $this->mappings->get($entity);
    }

    /**
     * @param string $entity
     *
     * @return null|\Ollieread\Articulate\Contracts\EntityRepository
     * @throws \InvalidArgumentException
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
     * @param bool   $persisted
     *
     * @return \Ollieread\Articulate\Contracts\Entity
     * @throws \InvalidArgumentException
     */
    public function hydrate(string $entityClass, $attributes = [], bool $persisted = true): Entity
    {
        if ($attributes instanceof Entity) {
            throw new \InvalidArgumentException('Entity is already hydrated');
        }

        if (empty($attributes)) {
            throw new \InvalidArgumentException('No attributes provided for entity hydration');
        }

        if ($attributes instanceof Collection) {
            throw new \InvalidArgumentException('Collections cannot be hydrated');
        }

        if (! class_exists($entityClass)) {
            throw new \InvalidArgumentException(sprintf('Invalid entity class provided %s', $entityClass));
        }

        $mapping = $this->getMapping($entityClass);
        /**
         * @var \Ollieread\Articulate\Contracts\Entity $entity
         */
        $entity = app()->make($entityClass);

        // Populate any default attributes if needed
        // We aren't using toArray() because some of the default values may be Arrayable
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $mapping
            ->getColumns()
            ->keyBy(function (Column $column) {
                return $column->getColumnName();
            })
            ->map(function (Column $column) {
                return $column->getDefault();
            })
            ->merge($attributes)
            ->each(function ($value, $key) use ($mapping, $entity) {
                $column = $mapping->getColumn($key);

                if ($column) {
                    $attributeName = $column->getAttributeName();
                    $columnName    = $column->getColumnName();

                    // If a mapping has a different column name, we want to actually set that attribute
                    // simply because it's useful to have that data
                    if ($columnName && $attributeName !== $columnName) {
                        $entity->setAttribute($columnName, $value);
                        $key = $attributeName;
                    }

                    // If a column mapping exists, we wan't to cast it, which we don't want to do before
                    // we do the above
                    $value = $column->cast($value);
                }

                $entity->setAttribute($key, $value);
            });

        // We call the hydrated method as a sort of event, sometimes dynamic properties will be set here
        $entity::hydrated($entity);
        // Now that we're all done, we'll clean so that the entity doesn't appear to be dirty
        $entity->clean();

        if ($persisted) {
            // We can assume that the entity has been 'persisted', which means that it exists in the database
            // so we set that flag here
            $entity->setPersisted();
        }

        return $entity;
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Entity $entity
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function dehydrate(Entity $entity): array
    {
        $mapping = $this->getMapping(\get_class($entity));

        return collect($entity->getAll())->mapWithKeys(function ($value, $key) use($mapping) {
            $column = $mapping->getColumn($key);

            if ($column) {
                return $column->toDatabase($value);
            }

            return $value;
        });
    }
}