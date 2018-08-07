<?php

namespace Sprocketbox\Articulate\Sources\Illuminate;

use BadMethodCallException;
use Closure;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Sprocketbox\Articulate\Attributes\EntityAttribute;
use Sprocketbox\Articulate\Contracts\EntityMapping;
use Sprocketbox\Articulate\Contracts\Resolver;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\EntityManager;
use Sprocketbox\Articulate\Support\Collection;

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class IlluminateBuilder
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * @var \Sprocketbox\Articulate\EntityManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    protected $mapping;

    /**
     * All of the globally registered builder macros.
     *
     * @var array
     */
    protected static $macros = [];

    /**
     * All of the locally registered builder macros.
     *
     * @var array
     */
    protected $localMacros = [];

    /**
     * @var array
     */
    protected $with = [];

    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passthru = [
        'insert',
        'insertGetId',
        'getBindings',
        'toSql',
        'exists',
        'doesntExist',
        'count',
        'min',
        'max',
        'avg',
        'sum',
        'getConnection',
        'recursive',
        'delete',
    ];

    public function __construct(QueryBuilder $query, EntityManager $entityManager, string $entity, ?EntityMapping $mapping = null)
    {
        $this->setQuery($query);
        $this->manager = $entityManager;
        $this->mapping = $mapping;
        $this->setEntity($entity);
    }

    protected function newCollection($items = [])
    {
        return new Collection($items);
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        if (! $this->mapping || $this->mapping->getEntity() !== $entity) {
            $this->mapping = $this->manager->getEntityMapping($entity);
        }

        return $this;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function make(): Entity
    {
        $entityClass = $this->entity;

        return new $entityClass;
    }

    public function setQuery(QueryBuilder $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function with(...$entities)
    {
        $this->with = array_merge($this->with, $entities);
        return $this;
    }

    public function has($entity)
    {
        $attribute = $this->mapping->getAttribute($entity);

        if ($attribute && $attribute instanceof EntityAttribute) {
            $resolver = $attribute->getResolver();

            if ($resolver) {
                $mapping = $this->manager->getEntityMapping($attribute->getEntityClass());
                $this->whereExists(function (IlluminateBuilder $builder) use($resolver, $mapping) {
                    if ($resolver instanceof Closure) {
                        $resolver($builder, $this->mapping, $mapping);
                    } else if ($resolver instanceof Resolver) {
                        $resolver->has($builder, $this->mapping, $mapping);
                    }
                });
            }
        }
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery(): QueryBuilder
    {
        return $this->query;
    }

    public function toBase(): QueryBuilder
    {
        return $this->getQuery();
    }

    public function first($columns = ['*'])
    {
        return $this->take(1)->get($columns)->first();
    }

    public function get($columns = ['*'])
    {
        return $this->newCollection($this->manager->hydrate($this->getEntity(), $this->query->get($columns)));
    }

    protected function getWith(\stdClass $row): \stdClass
    {
        if ($this->with) {
            $data = (array)$row;
            collect($this->with)->mapWithKeys(function ($entity, $key) use ($data) {
                $conditions = null;

                if (! is_numeric($key)) {
                    $attributeName = $key;
                    $conditions    = $entity;
                } else {
                    $attributeName = $entity;
                }

                $attribute = $this->mapping->getAttribute($attributeName);

                if ($attribute && $attribute instanceof EntityAttribute) {
                    $resolver = $attribute->getResolver();

                    if ($resolver) {
                        $repository = $this->manager->repository($attribute->getEntityClass());

                        if ($resolver instanceof Closure) {
                            return [$attributeName => $resolver($repository, $data, $conditions)];
                        }

                        if ($resolver instanceof Resolver) {
                            return [$attributeName => $resolver->get($repository, $data, $conditions)];
                        }

                        throw new \RuntimeException(sprintf('Invalid entity resolver for %s on $s', $attributeName, $this->entity));
                    }
                }

                throw new \InvalidArgumentException(sprintf('Cannot load %s for %s, it isn\'t a mapped entity attribute', $attributeName, $this->entity));
            })->each(function ($data, $attribute) use (&$row) {
                $row->{$attribute} = $data;
            });
        }

        return $row;
    }

    /**
     * Get the given macro by name.
     *
     * @param  string $name
     *
     * @return \Closure
     */
    public function getMacro($name)
    {
        return Arr::get($this->localMacros, $name);
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method === 'macro') {
            $this->localMacros[$parameters[0]] = $parameters[1];

            return null;
        }

        if (isset($this->localMacros[$method])) {
            array_unshift($parameters, $this);

            return $this->localMacros[$method](...$parameters);
        }

        if (isset(static::$macros[$method])) {
            if (static::$macros[$method] instanceof Closure) {
                return \call_user_func_array(static::$macros[$method]->bindTo($this, static::class), $parameters);
            }

            return \call_user_func_array(static::$macros[$method], $parameters);
        }

        if (\in_array($method, $this->passthru, true)) {
            return $this->toBase()->{$method}(...$parameters);
        }

        $this->query->{$method}(...$parameters);

        return $this;
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'macro') {
            static::$macros[$parameters[0]] = $parameters[1];

            return null;
        }

        if (! isset(static::$macros[$method])) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $method
            ));
        }

        if (static::$macros[$method] instanceof Closure) {
            return \call_user_func_array(Closure::bind(static::$macros[$method], null, static::class), $parameters);
        }

        return \call_user_func_array(static::$macros[$method], $parameters);
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}