<?php

namespace Ollieread\Articulate\Repositories;

use Illuminate\Database\DatabaseManager;
use Ollieread\Articulate\Database\Builder;
use Ollieread\Articulate\Mapper;

class EntityRepository
{
    /**
     * @var string
     */
    private $_entity;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    private $_database;

    /**
     * @var \Ollieread\Articulate\Mapper
     */
    private $_mapper;

    public function __construct(DatabaseManager $database, Mapper $mapper)
    {
        $this->_database = $database;
        $this->_mapper   = $mapper;
        $this->_entity   = $mapper->getEntity();
    }

    protected function query(): Builder
    {
        $connection = $this->_mapper->getConnection();
        $table      = $this->_mapper->getTable();
        $builder    = new Builder($this->_database->connection($connection),
            $this->_database->getQueryGrammar(),
            $this->_database->getPostProcessor(),
            $this->_entity);

        return $builder->from($table);
    }
}