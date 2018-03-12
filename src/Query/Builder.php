<?php

namespace Ollieread\Articulate\Query;

use Closure;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Collection;
use Ollieread\Articulate\EntityManager;

/**
 * Class Builder
 *
 * @package Ollieread\Articulate\Database
 */
class Builder extends QueryBuilder
{
    /**
     * @var \Ollieread\Articulate\EntityManager
     */
    protected $_manager;

    /**
     * @var string
     */
    protected $_entity;

    /**
     * @var array
     */
    protected $_aliases = [];

    /**
     * @var array
     */
    protected $_with = [];

    /**
     * Whether or not to hydrate
     *
     * @var bool
     */
    protected $hydrate = true;

    /**
     * Builder constructor.
     *
     * @param \Illuminate\Database\ConnectionInterface             $connection
     * @param \Illuminate\Database\Query\Grammars\Grammar|null     $grammar
     * @param \Illuminate\Database\Query\Processors\Processor|null $processor
     * @param \Ollieread\Articulate\EntityManager                  $manager
     */
    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar = null,
        Processor $processor = null,
        EntityManager $manager)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->_manager = $manager;
    }

    /**
     * Create an alias for the provided table name
     *
     * @param string $table
     *
     * @return string
     */
    protected function tableAlias(string $table)
    {
        $tableAlias                  = $this->createTableAlias($table);
        $this->_aliases[$tableAlias] = $table;

        return $tableAlias;
    }

    public function createTableAlias(string $table)
    {
        $tableParts = explode('_', snake_case($table));
        $tableAlias = '';

        array_walk($tableParts, function ($value) use (&$tableAlias) {
            $tableAlias .= strtolower($value[0]);
        });

        return $tableAlias . str_random(3);
    }

    /**
     * Disable entity hydration for this query
     *
     * @return $this
     */
    public function noHydrate()
    {
        $this->hydrate = false;

        return $this;
    }

    /**
     * @return \Ollieread\Articulate\EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->_manager;
    }

    /**
     * Create a new instance of the entity this builder was for
     *
     * @return mixed
     */
    public function newEntityInstance()
    {
        $entity = $this->_entity;

        return new $entity;
    }

    /**
     * Hydrate a row
     *
     * @param array $attributes
     *
     * @return array|null|\Ollieread\Articulate\Entities\BaseEntity
     */
    private function hydrate($attributes = [])
    {
        return $this->hydrate ? $this->_manager->hydrate($this->_entity, (array) $attributes) : $attributes;
    }

    /**
     * Hydrate a collection
     *
     * @param \Illuminate\Support\Collection $results
     *
     * @return \Illuminate\Support\Collection
     */
    private function hydrateAll(Collection $results)
    {
        return $results->map(function ($result) {
            return $this->hydrate($result);
        });
    }

    /**
     * @param string[] ...$relationships
     *
     * @return $this
     */
    public function with(string ...$relationships)
    {
        $this->_with = array_merge($this->_with, $relationships);

        return $this;
    }

    public function newQuery()
    {
        return new static($this->connection, $this->grammar, $this->processor, $this->_manager);
    }

    /**
     * Set the table for the entity
     *
     * @param             $entity
     * @param null|string $alias
     *
     * @return $this
     */
    public function for($entity, ?string $alias = null)
    {
        $this->_entity = $entity;
        $table         = $this->_manager->getMapping($entity)->getTable();

        if (in_array($alias, $this->_aliases)) {
            $alias = $this->tableAlias($table);
        }

        return $this->from($table . ' as ' . $alias);
    }

    /**
     * Get the results for the current query
     *
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection
     */
    protected function results(array $columns = ['*']): Collection
    {
        $original = $this->columns;

        if (is_null($original)) {
            $this->columns = $columns;
        }

        $results       = $this->processor->processSelect($this, $this->runSelect());
        $this->columns = $original;
        $results       = collect($results);
        $this->processWith($results);

        return $results;
    }

    /**
     * Load the relationships, if any were set
     *
     * @param $results
     */
    protected function processWith(&$results)
    {
        if ($this->_with) {
            $mapper  = $this->_manager->getMapping($this->_entity);
            $allWith = $this->_with;

            foreach ($allWith as $with) {
                $relationship = $mapper->getRelationship($with);

                if ($relationship) {
                    $relationship->load($results);
                }
            }
        }
    }

    /**
     * Get all results for the current query
     *
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function get($columns = ['*']): Collection
    {
        $results = $this->results($columns);

        return $this->hydrateAll($results);
    }

    /**
     * Get one (the first) row for the current query
     *
     * @param array $columns
     *
     * @return array|\Illuminate\Database\Eloquent\Model|null|object|\Ollieread\Articulate\Entities\BaseEntity|static
     */
    public function first($columns = ['*'])
    {
        $results = $this->results($columns);
        $first   = $results->first();

        return $this->hydrate($first);
    }

    /**
     * Add a join clause to the query
     *
     * This method behaves exactly like the default, except it uses a custom JoinClause, and can
     * optionally use an entity rather than table
     *
     * @param string $table
     * @param string $first
     * @param null   $operator
     * @param null   $second
     * @param string $type
     * @param bool   $where
     *
     * @return $this
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $mapping = $this->_manager->getMapping($table);

        if ($mapping) {
            $table = $mapping->getTable();
        }

        $join = new JoinClause($this, $type, $table);

        // If the first "column" of the join is really a Closure instance the developer
        // is trying to build a join with a complex "on" clause containing more than
        // one condition, so we'll add the join and call a Closure with the query.
        if ($first instanceof Closure) {
            call_user_func($first, $join);
            $this->joins[] = $join;
            $this->addBinding($join->getBindings(), 'join');
        }

        // If the column is simply a string, we can assume the join simply has a basic
        // "on" clause with a single condition. So we will just build the join with
        // this simple join clauses attached to it. There is not a join callback.
        else {
            $method = $where ? 'where' : 'on';
            $this->joins[] = $join->$method($first, $operator, $second);
            $this->addBinding($join->getBindings(), 'join');
        }

        return $this;
    }
}