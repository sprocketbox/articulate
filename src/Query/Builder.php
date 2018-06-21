<?php

namespace Ollieread\Articulate\Query;

use BadMethodCallException;
use Closure;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Ollieread\Articulate\Contracts\Entity;
use Ollieread\Articulate\EntityManager;
use Ollieread\Articulate\Support\Collection;

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * @var \Ollieread\Articulate\EntityManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $entity;

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
    ];

    public function __construct(QueryBuilder $query, EntityManager $entityManager)
    {
        $this->setQuery($query);
        $this->manager = $entityManager;
    }

    protected function newCollection($items = [])
    {
        return new Collection($items);
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
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

    public function with(string... $relationships)
    {
        $this->with = array_merge($this->with, $relationships);
        return $this;
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
        return $this->newCollection($this->query->get($columns)->map(function (\stdClass $row) {
            return $this->manager->hydrate($this->entity, $row);
        }));
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

            return;
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

            return;
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