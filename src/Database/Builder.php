<?php

namespace Ollieread\Articulate\Database;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Collection;
use Ollieread\Articulate\Entities;
use Ollieread\Articulate\Mapper;

class Builder extends QueryBuilder
{
    /**
     * @var \Ollieread\Articulate\Mapper
     */
    protected $mapper;

    /**
     * @var \Ollieread\Articulate\Entities
     */
    protected $manager;

    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar = null,
        Processor $processor = null,
        Entities $manager,
        Mapper $mapper)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->mapper  = $mapper;
        $this->manager = $manager;
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
        $entity = $this->mapper->getEntity();

        return new $entity;
    }

    private function hydrate($attributes = [])
    {
        $entity = $this->newEntityInstance();

        foreach ($attributes as $key => $value) {
            $setter = 'set' . studly_case($key);

            if (method_exists($entity, $setter)) {
                $entity->{$setter}($value);
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