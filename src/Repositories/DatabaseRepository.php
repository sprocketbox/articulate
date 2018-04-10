<?php

namespace Ollieread\Articulate\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Entities\BaseEntity;

/**
 * Class DatabaseRepository
 *
 * @package Ollieread\Articulate\Repositories
 */
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
            return $this->getOneBy(snake_case(substr($name, 5)), $arguments[0]);
        }

        return null;
    }

    /**
     * @param null|string $entity
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query(?string $entity = null)
    {
        $database = app(DatabaseManager::class);

        $entity = $entity ?? $this->entity();

        if ($entity) {
            $mapping    = ($entity === $this->entity() ? $this->mapping() : $this->manager()->getMapping($entity));
            $connection = $mapping->getConnection();
            $table      = $mapping->getTable();

            return $database->connection($connection)->query()->from($table);
        }

        return $database->connection()->query();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getQuery()
    {
        $query = $this->query($this->entity());

        if (\func_num_args() === 2) {
            [$column, $value] = \func_get_args();
            $method = \is_array($value) ? 'whereIn' : 'where';
            $query  = $value instanceof Expression ? $query->$method($value) : $query->$method($column, $value);
        } elseif (\func_num_args() === 1) {
            $columns = \func_get_arg(0);

            if (\is_array($columns)) {
                foreach ($columns as $column => $value) {
                    $method = \is_array($value) ? 'whereIn' : 'where';
                    $query  = $value instanceof Expression ? $query->$method($value) : $query->$method($column, $value);
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
     * @param mixed $identifier
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity
     */
    public function load($identifier)
    {
        $keyName = $this->mapping()->getKey();

        return $this->getOneBy($keyName, $identifier);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param int                                $count
     * @param string                             $pageName
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginate(Builder $query, int $count, string $pageName = 'page'): LengthAwarePaginatorContract
    {
        $total     = $query->getCountForPagination();
        $paginator = null;

        $page    = Paginator::resolveCurrentPage($pageName);
        $results = $query->forPage($page, $count)->get();

        if ($results) {
            $results = $this->hydrate($results);
        }

        return new LengthAwarePaginator($results, $total, $count, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
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
            $insert   = ! $entity->isPersisted();

            $fields = [];

            $columns = $this->mapping()->getColumns();
            $columns->each(function (Column $column, string $name) use ($entity, &$fields) {
                if (! $column->isImmutable() && ! $column->isDynamic() && $entity->isDirty($name)) {
                    $fields[$column->getColumnName()] = $column->toDatabase($entity->get($name));
                }
            });

            if (\count($fields)) {
                $now = Carbon::now();

                if ($insert) {
                    if (! isset($fields['created_at']) && $columns->has('created_at')) {
                        $fields['created_at'] = $columns->get('created_at')->toDatabase($now);
                    }
                    if (! isset($fields['updated_at']) && $columns->has('updated_at')) {
                        $fields['updated_at'] = $columns->get('updated_at')->toDatabase($now);
                    }

                    $newKeyValue = $this->query($this->entity())->insertGetId($fields);

                    if (empty($keyValue) && ! empty($newKeyValue)) {
                        $entity->set($keyName, $newKeyValue);
                    }

                    $entity->setPersisted();
                } else {
                    if ($columns->has('updated_at')) {
                        $fields['updated_at'] = $columns->get('updated_at')->toDatabase($now);
                    }

                    $this->query($this->entity())->where($keyName, '=', $keyValue)->update($fields);
                }

                return $entity;
            }
        }

        return null;
    }
}