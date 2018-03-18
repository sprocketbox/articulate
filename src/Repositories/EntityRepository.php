<?php

namespace Ollieread\Articulate\Repositories;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Entities\BaseEntity;
use Ollieread\Articulate\EntityManager;
use Ollieread\Articulate\Mapping;
use Ollieread\Articulate\Query\Builder;

class EntityRepository
{
    /**
     * @var string
     */
    private $_entity;

    /**
     * @var \Ollieread\Articulate\EntityManager
     */
    private $_manager;

    /**
     * @var \Ollieread\Articulate\Mapping
     */
    private $_mapper;

    /**
     * EntityRepository constructor.
     *
     * @param \Ollieread\Articulate\EntityManager $manager
     * @param \Ollieread\Articulate\Mapping       $mapper
     */
    public function __construct(EntityManager $manager, Mapping $mapper)
    {
        $this->_manager = $manager;
        $this->_mapper  = $mapper;
        $this->_entity  = $mapper->getEntity();
    }

    /**
     * Magic method handling for dynamic functions such as getByAddress() or getOneById().
     *
     * @param       $name
     * @param array $arguments
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed|null
     */
    public function __call($name, $arguments = [])
    {
        if (\count($arguments) > 1) {
            // TODO: Should probably throw an exception here
            return null;
        }

        if (0 === strpos($name, 'getBy')) {
            return $this->getBy(snake_case(substr($name, 5)), $arguments[0]);
        }

        if (0 === strpos($name, 'getOneBy')) {
            $column = snake_case(substr($name, 8));

            return \call_user_func([$this->make(), 'where'], $column, $arguments[0])->first();
        }

        return null;
    }

    /**
     * @param null|string $entity
     *
     * @return \Ollieread\Articulate\Query\Builder
     * @throws \RuntimeException
     */
    protected function query(?string $entity = null): Builder
    {
        return $this->_manager->newQueryBuilder($entity ?? $this->_entity);
    }

    /**
     * @return \Ollieread\Articulate\Query\Builder
     * @throws \RuntimeException
     */
    protected function getQuery(): Builder
    {
        $query = $this->query();

        if (\func_num_args() === 2) {
            list($column, $value) = \func_get_args();
            $method = \is_array($value) ? 'whereIn' : 'where';
            $query  = $query->$method($column, $value);
        } elseif (\func_num_args() === 1) {
            $columns = \func_get_args();

            if (\is_array($columns)) {
                foreach ($columns as $column => $value) {
                    $method = \is_array($value) ? 'whereIn' : 'where';
                    $query  = $query->$method($column, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Helper method for retrieving entities by a column or array of columns.
     *
     * @return mixed
     */
    public function getBy(): ?Collection
    {
        return \call_user_func_array([$this, 'getQuery'], func_get_args())->get();
    }

    /**
     * Helper method for retrieving an entity by a column or array of columns.
     *
     * @return mixed
     */
    public function getOneBy(): ?BaseEntity
    {
        return \call_user_func_array([$this, 'getQuery'], func_get_args())->first();
    }

    /**
     * @param \Ollieread\Articulate\Entities\BaseEntity $entity
     *
     * @return \Ollieread\Articulate\Entities\BaseEntity
     * @throws \RuntimeException
     */
    public function save(BaseEntity $entity): ?BaseEntity
    {
        if (\get_class($entity) === $this->_entity) {
            $keyName  = $this->_mapper->getKey();
            $keyValue = $entity->get($keyName);

            $fields = [];

            $columns = $this->_mapper->getColumns();
            $columns->each(function (Column $column, string $name) use ($entity, &$fields) {
                if (! $column->isImmutable() && ! $column->isDynamic() && $entity->isDirty($name)) {
                    $fields[$name] = $column->toDatabase($entity->get($name));
                }
            });

            if (\count($fields)) {
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

    /**
     * @param \Ollieread\Articulate\Entities\BaseEntity $entity
     *
     * @return int
     */
    public function delete(BaseEntity $entity): int
    {
        if (\get_class($entity) === $this->_entity) {
            $keyName  = $this->_mapper->getKey();
            $keyValue = $entity->get($keyName);

            return $this->query()->delete($keyValue);
        }

        return 0;
    }
}