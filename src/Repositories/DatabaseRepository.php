<?php

namespace Ollieread\Articulate\Repositories;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Entities\BaseEntity;

class DatabaseRepository extends EntityRepository
{

    /**
     * Magic method handling for dynamic functions such as getByAddress() or getOneById().
     *
     * @param       $name
     * @param array $arguments
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed|null
     * @throws \RuntimeException
     */
    public function __call($name, array $arguments = [])
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(?string $entity = null): Builder
    {
        $database = app(DatabaseManager::class);

        if ($entity) {
            $mapping    = ($entity === $this->entity() ? $this->mapping() : $this->manager()->getMapping($entity));
            $connection = $mapping->getConnection();
            $table      = $mapping->getTable();

            return $database->connection($connection)->query()->from($table);
        }

        return $database->connection()->query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQuery(): Builder
    {
        $query = $this->query();

        if (\func_num_args() === 2) {
            [$column, $value] = \func_get_args();
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
     * @throws \RuntimeException
     */
    public function getBy(): ?Collection
    {
        $results = \call_user_func_array([$this, 'getQuery'], \func_get_args())->get();

        if ($results) {
            return $this->hydrate($results);
        }

        return new Collection;
    }

    /**
     * Helper method for retrieving an entity by a column or array of columns.
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function getOneBy(): ?BaseEntity
    {
        $results = \call_user_func_array([$this, 'getQuery'], \func_get_args())->first();

        if ($results) {
            return $this->hydrate($results);
        }

        return null;
    }

    /**
     * @param \Ollieread\Articulate\Entities\BaseEntity $entity
     *
     * @return \Ollieread\Articulate\Entities\BaseEntity
     * @throws \RuntimeException
     */
    public function save(BaseEntity $entity): ?BaseEntity
    {
        if (\get_class($entity) === $this->entity()) {
            $keyName  = $this->mapping()->getKey();
            $keyValue = $entity->get($keyName);
            $insert   = empty($keyValue);

            $fields = [];

            $columns = $this->mapping()->getColumns();
            $columns->each(function (Column $column, string $name) use ($entity, &$fields) {
                if (! $column->isImmutable() && ! $column->isDynamic() && $entity->isDirty($name)) {
                    $fields[$name] = $column->toDatabase($entity->get($name));
                }
            });

            if (\count($fields)) {
                $now = Carbon::now();

                if ($insert) {
                    if ($fields['created_at']) {
                        $fields['created_at'] = $now;
                    }
                    if ($fields['updated_at']) {
                        $fields['updated_at'] = $now;
                    }

                    $keyValue = $this->query()->insertGetId($fields);
                    $entity->set($keyName, $keyValue);
                } else {
                    if ($fields['updated_at']) {
                        $fields['updated_at'] = $now;
                    }

                    $this->query()->where($keyName, '=', $keyValue)->update($fields);
                }

                return $entity;
            }
        }

        return null;
    }

    /**
     * @param \Ollieread\Articulate\Entities\BaseEntity $entity
     *
     * @return int
     */
    public function delete(BaseEntity $entity): int
    {
        if (\get_class($entity) === $this->entity()) {
            $keyName  = $this->mapping()->getKey();
            $keyValue = $entity->get($keyName);

            return $this->query()->delete($keyValue);
        }

        return 0;
    }

    /**
     * @return \Ollieread\Articulate\Entities\BaseEntity
     */
    private function make(): BaseEntity
    {
        $entityClass = $this->entity();

        return new $entityClass;
    }
}