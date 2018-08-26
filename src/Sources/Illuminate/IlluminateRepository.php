<?php

namespace Sprocketbox\Articulate\Sources\Illuminate;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Contracts\Pagination\Paginator as SimplePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Sprocketbox\Articulate\Attributes\EntityAttribute;
use Sprocketbox\Articulate\Contracts\Attribute;
use Sprocketbox\Articulate\Contracts\Criteria;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\Repositories\Repository;
use Sprocketbox\Articulate\Support\Collection;

class IlluminateRepository extends Repository
{
    /**
     * @param null|string $entity
     *
     * @return \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder
     */
    protected function query(?string $entity = null): IlluminateBuilder
    {
        return $this->source()->builder($entity, $this->mapping());
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Criteria ...$criteria
     *
     * @return \Sprocketbox\Articulate\Support\Collection
     */
    public function getByCriteria(Criteria... $criteria): Collection
    {
        $result = $this->pushCriteria(...$criteria)
                       ->applyCriteria($this->query())
                       ->get() ?? new Collection;

        $this->resetCriteria();

        return $result;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Criteria ...$criteria
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity
     */
    public function getOneByCriteria(Criteria... $criteria): ?Entity
    {
        $result = $this->pushCriteria(...$criteria)
                       ->applyCriteria($this->query())
                       ->first();

        $this->resetCriteria();

        return $result;
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
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
     * @return null|\Sprocketbox\Articulate\Entities\Entity
     */
    public function load($identifier)
    {
        $keyName = $this->mapping()->getKey();

        return $this->getOneByCriteria($keyName, $identifier);
    }

    /**
     * @param \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
     * @param int                                                          $perPage
     * @param string                                                       $pageName
     * @param null                                                         $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginate($query, int $perPage, string $pageName = 'page', $page = null): LengthAwarePaginatorContract
    {
        $total     = $query->toBase()->getCountForPagination();
        $paginator = null;

        $page  = $page ?: Paginator::resolveCurrentPage($pageName);
        $items = $query->forPage($page, $perPage)->get();

        $options = [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'page', 'options'
        ));
    }

    /**
     * @param \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
     * @param int                                                          $perPage
     * @param string                                                       $pageName
     * @param null                                                         $page
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    protected function simplePaginate($query, int $perPage, string $pageName = 'page', $page = null): SimplePaginatorContract
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        // Next we will set the limit and offset for this query so that when we get the
        // results we get the proper section of results. Then, we'll create the full
        // paginator instances for these results with the given page and per page.
        $query->skip(($page - 1) * $perPage)->take($perPage + 1);
        $items   = $query->get();
        $options = [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return Container::getInstance()->makeWith(Paginator::class, compact(
            'items', 'perPage', 'page', 'options'
        ));
    }

    /**
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     *
     * @return \Sprocketbox\Articulate\Entities\Entity
     * @throws \RuntimeException
     */
    public function persist(Entity $entity): Entity
    {
        if ($this->mapping()->isReadOnly()) {
            throw new \RuntimeException(sprintf('Cannot persist read only entity %s', $this->entity()));
        }

        if (\get_class($entity) !== $this->entity()) {
            throw new \InvalidArgumentException(sprintf('Entity %s does not belong to the repository %s', \get_class($entity), \get_class($this)));
        }

        $keyName          = $this->mapping()->getKey();
        $keyValue         = $entity->get($keyName);
        $insert           = ! $entity->isPersisted();
        $attributes       = $this->mapping()->getAttributes();
        $entities         = [];
        $fields           = $this->getDirty($entity);
        $entityAttributes = $attributes->filter(function (Attribute $attribute) {
            return $attribute instanceof EntityAttribute && $attribute->shouldCascade();
        })->mapWithKeys(function (EntityAttribute $attribute) {
            return [$attribute->getEntityClass() => $attribute];
        });

        $attributes->each(function (EntityAttribute $attribute) use (&$entities, $fields) {
            $childEntity = $fields[$attribute->getName()] ?? null;

            if ($childEntity) {
                $entities[$attribute->getEntityClass()] = $childEntity;
            }
        });

        $fields = collect($fields)->filter(function ($value, $key) use ($attributes) {
            $attribute = $attributes->get($key);

            if ($attribute) {
                return ! $attribute->isDynamic();
            }

            return true;
        })->toArray();

        if (\count($fields)) {
            $now = Carbon::now();

            if ($insert) {
                if (! isset($fields['created_at']) && $attributes->has('created_at')) {
                    $fields['created_at'] = $attributes->get('created_at')->parse($now);
                }
                if (! isset($fields['updated_at']) && $attributes->has('updated_at')) {
                    $fields['updated_at'] = $attributes->get('updated_at')->parse($now);
                }

                $newKeyValue = $this->query($this->entity())->insertGetId($fields);

                if (empty($keyValue) && ! empty($newKeyValue)) {
                    $entity->set($keyName, $attributes->get($keyName)->parse($newKeyValue));
                }

                $entity->setPersisted();
            } else {
                if ($attributes->has('updated_at')) {
                    $fields['updated_at'] = $attributes->get('updated_at')->parse($now);
                }

                $this->query($this->entity())->where($keyName, '=', $keyValue)->update($fields);
            }
        }

        if ($entities) {
            collect($entities)->each(function ($relatedEntity, string $entityClass) use ($entity, $entityAttributes) {
                $repository = $this->manager()->repository($entityClass);

                if ($repository) {
                    if ($relatedEntity instanceof \Illuminate\Support\Collection) {
                        $relatedEntity = $relatedEntity->toArray();
                    }

                    if (\is_array($relatedEntity)) {
                        array_walk($relatedEntity, function (Entity $entity) use ($repository) {
                            $repository->persist($entity);
                        });
                    } else if ($relatedEntity instanceof Entity) {
                        $repository->persist($relatedEntity);
                    }
                }

                /**
                 * @var EntityAttribute|null $attribute
                 */
                $attribute = $entityAttributes->get($entityClass);
                $resolver  = $attribute->getResolver() ?? null;

                if ($resolver) {
                    $resolver->persistRelated($entity, $relatedEntity);
                }
            });
        }

        return $entity;
    }
}