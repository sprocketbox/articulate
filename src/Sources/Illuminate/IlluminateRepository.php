<?php

namespace Sprocketbox\Articulate\Sources\Illuminate;

use Illuminate\Pagination\LengthAwarePaginator;
use Sprocketbox\Articulate\Entities\Entity;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\Paginator;
use Sprocketbox\Articulate\Repositories\Repository;
use Sprocketbox\Articulate\Support\Collection;
use Sprocketbox\Articulate\Contracts\Attribute;
use Sprocketbox\Articulate\Contracts\Criteria;

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
        collect($criteria)->each([$this, 'pushCriteria']);
        return $this->applyCriteria($this->query())->get() ?? new Collection;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Criteria ...$criteria
     *
     * @return null|\Sprocketbox\Articulate\Entities\Entity
     */
    public function getOneByCriteria(Criteria... $criteria): ?Entity
    {
        collect($criteria)->each([$this, 'pushCriteria']);
        return $this->applyCriteria($this->query())->first();
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
     * @param int                                                          $count
     * @param string                                                       $pageName
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginate($query, int $count, string $pageName = 'page'): LengthAwarePaginatorContract
    {
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
     * @param \Sprocketbox\Articulate\Entities\Entity $entity
     *
     * @return \Sprocketbox\Articulate\Entities\Entity
     * @throws \RuntimeException
     */
    public function persist(Entity $entity): ?Entity
    {
        if (\get_class($entity) === $this->entity()) {
            $keyName  = $this->mapping()->getKey();
            $keyValue = $entity->get($keyName);
            $insert   = ! $entity->isPersisted();

            // todo: Cascade saving to child entities
            $attributes = $this->mapping()->getAttributes();
            $fields = $this->getDirty($entity);

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
                        $entity->set($keyName, $newKeyValue);
                    }

                    $entity->setPersisted();
                } else {
                    if ($attributes->has('updated_at')) {
                        $fields['updated_at'] = $attributes->get('updated_at')->parse($now);
                    }

                    $this->query($this->entity())->where($keyName, '=', $keyValue)->update($fields);
                }

                return $entity;
            }
        }
    }
}