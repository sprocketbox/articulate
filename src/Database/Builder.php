<?php

namespace Ollieread\Articulate\Database;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Collection;
use Ollieread\Articulate\EntityManager;

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

    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar = null,
        Processor $processor = null,
        EntityManager $manager)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->_manager = $manager;
    }

    public function setEntity(string $_entity): Builder
    {
        $this->_entity = $_entity;

        return $this;
    }

    public function get($columns = ['*'])
    {
        $original = $this->columns;

        if (is_null($original)) {
            $this->columns = $columns;
        }

        $results = $this->processor->processSelect($this, $this->runSelect());

        $this->columns = $original;

        return $this->hydrateAll($results);
    }

    public function newEntityInstance()
    {
        $entity = $this->_entity;

        return new $entity;
    }

    private function hydrate($attributes = [])
    {
        $entity  = $this->newEntityInstance();
        $mapper  = $this->_manager->getMapping($this->_entity);

        foreach ($attributes as $key => $value) {
            $setter = 'set' . studly_case($key);

            if (method_exists($entity, $setter)) {
                $column = $mapper->getColumn($key);
                $entity->{$setter}($column->cast($value));
            }
        }

        return $entity;
    }

    private function hydrateAll(array $results)
    {
        $collection = new Collection;

        foreach ($results as $row) {
            $collection->push($this->hydrate($row));
        }

        return $collection;
    }
}