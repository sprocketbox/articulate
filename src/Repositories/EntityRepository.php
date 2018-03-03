<?php

namespace Ollieread\Articulate\Repositories;

use Illuminate\Database\DatabaseManager;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Database\Builder;
use Ollieread\Articulate\Entities\BaseEntity;
use Ollieread\Articulate\EntityManager;
use Ollieread\Articulate\Mapping;

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
     * @var \Ollieread\Articulate\EntityManager
     */
    private $_manager;

    /**
     * @var \Ollieread\Articulate\Mapping
     */
    private $_mapper;

    public function __construct(DatabaseManager $database, EntityManager $manager, Mapping $mapper)
    {
        $this->_database = $database;
        $this->_manager  = $manager;
        $this->_mapper   = $mapper;
        $this->_entity   = $mapper->getEntity();
    }

    protected function query(): Builder
    {
        $connection = $this->_mapper->getConnection();
        $builder    = new Builder(
            $this->_database->connection($connection),
            $this->_database->getQueryGrammar(),
            $this->_database->getPostProcessor(),
            $this->_manager);

        return $builder->for($this->_entity);
    }

    public function save(BaseEntity $entity)
    {
        if (get_class($entity) === $this->_entity) {
            $keyName = $this->_mapper->getKey();
            $keyValue = $entity->get($keyName);

            $fields = [];

            $columns = $this->_mapper->getColumns();
            $columns->each(function (Column $column, string $name) use ($entity, &$fields) {
                if (! $column->isImmutable() && ! $column->isDynamic() && $entity->isDirty($name)) {
                    $fields[$name] = $column->toDatabase($entity->get($name));
                }
            });

            if (count($fields)) {
                if (empty($keyValue)) {
                    $keyValue = $this->query()->insertGetId($fields);
                    $entity->set($keyName, $keyValue);

                    return $entity;
                } else {
                    $this->query()->where($keyName, '=', $keyValue)->update($fields);
                }
            }
        }
    }
}