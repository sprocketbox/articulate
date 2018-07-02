<?php

namespace Ollieread\Articulate\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Ollieread\Articulate\Support\Collection;
use Ollieread\Articulate\Concerns;
use Ollieread\Articulate\Contracts\Column;
use Ollieread\Articulate\Contracts\Criteria;
use Ollieread\Articulate\Contracts\Entity;
use Ollieread\Articulate\Criteria\WhereCriteria;
use Ollieread\Articulate\Criteria\WhereExpression;
use Ollieread\Articulate\Query\Builder;

/**
 * Class DatabaseRepository
 *
 * @package Ollieread\Articulate\Repositories
 */
class DatabaseRepository extends EntityRepository
{
    use Concerns\HandlesCriteria;

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
            throw new \InvalidArgumentException('Invalid argument count');
        }

        if (0 === strpos($name, 'getBy')) {
            return $this->getBy(snake_case(substr($name, 5)), $arguments[0]);
        }

        if (0 === strpos($name, 'getOneBy')) {
            return $this->getOneBy(snake_case(substr($name, 8)), $arguments[0]);
        }

        return null;
    }

    /**
     * @param null|string $entity
     *
     * @return \Ollieread\Articulate\Query\Builder
     */
    protected function query(?string $entity = null): Builder
    {
        $database = app(DatabaseManager::class);
        $entity   = $entity ?? $this->entity();

        if ($entity) {
            $mapping    = ($entity === $this->entity() ? $this->mapping() : $this->manager()->getMapping($entity));
            $connection = $mapping->getConnection();
            $table      = $mapping->getTable();

            $query = $database->connection($connection)->query()->from($table);
        } else {
            $query = $database->connection()->query();
        }

        return (new Builder($query, $this->manager()))->setEntity($entity);
    }

    /**
     * @return array
     */
    protected function getQueryCriteria(): array
    {
        $argCount  = \func_num_args();
        $arguments = \func_get_args();

        if ($argCount === 1 && \is_array($arguments)) {
            $arguments = $arguments[0];
        } else if (($argCount % 2) === 0) {
            $keys      = array_filter($arguments, function ($value, $key) {
                return $key === 0 || ($key % 2) === 0;
            }, ARRAY_FILTER_USE_BOTH);
            $values    = array_diff($arguments, $keys);
            $arguments = array_combine($keys, $values);
        }

        $criteria = [];

        if ($arguments) {
            foreach ($arguments as $column => $value) {
                if ($value instanceof Expression) {
                    $criteria[] = new WhereExpression($value);
                } else {
                    $criteria[] = new WhereCriteria($column, '=', $value);
                }
            }
        }

        return $criteria;
    }

    /**
     * Helper method for retrieving entities by a column or array of columns.
     *
     * @return \Ollieread\Articulate\Support\Collection
     *
     * @throws \RuntimeException
     */
    public function getBy(): Collection
    {
        return $this->getByCriteria(...$this->getQueryCriteria(...\func_get_args()));
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Criteria ...$criteria
     *
     * @return \Ollieread\Articulate\Support\Collection
     */
    public function getByCriteria(Criteria... $criteria): Collection
    {
        collect($criteria)->each([$this, 'pushCriteria']);
        return $this->applyCriteria($this->query())->get() ?? new Collection;
    }

    /**
     * Helper method for retrieving an entity by a column or array of columns.
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity
     * @throws \RuntimeException
     */
    public function getOneBy(): ?Entity
    {
        return $this->getOneByCriteria(...$this->getQueryCriteria(...\func_get_args()));
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Criteria ...$criteria
     *
     * @return null|\Ollieread\Articulate\Contracts\Entity
     */
    public function getOneByCriteria(Criteria... $criteria): ?Entity
    {
        collect($criteria)->each([$this, 'pushCriteria']);
        return $this->applyCriteria($this->query())->first();
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Entity $entity
     *
     * @return int
     */
    public function delete(Entity $entity): int
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
     * @param int    $count
     * @param string $pageName
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginate(int $count, string $pageName = 'page'): LengthAwarePaginatorContract
    {
        $query     = $this->applyCriteria($this->query());
        $total     = $query->toBase()->getCountForPagination();
        $paginator = null;

        $page    = Paginator::resolveCurrentPage($pageName);
        $results = $query->forPage($page, $count)->get();

        return new LengthAwarePaginator($results, $total, $count, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Entity $entity
     *
     * @return \Ollieread\Articulate\Contracts\Entity
     * @throws \RuntimeException
     */
    public function save(Entity $entity): ?Entity
    {
        if (\get_class($entity) === $this->entity()) {
            $keyName  = $this->mapping()->getKey();
            $keyValue = $entity->get($keyName);
            $insert   = ! $entity->isPersisted();

            $fields   = [];
            $entities = [];

            // todo: Cascade saving to child entities
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
    }
}