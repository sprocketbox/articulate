<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

use Sprocketbox\Articulate\Contracts\Repository;

class HasMany extends HasOne
{
    /**
     * @param \Sprocketbox\Articulate\Contracts\Repository $repository
     * @param string                                       $attribute
     * @param array|\Illuminate\Support\Collection         $data
     * @param \Closure|null                                $condition
     *
     * @return \Sprocketbox\Articulate\Entities\Entity|\Sprocketbox\Articulate\Support\Collection
     */
    public function get(Repository $repository, string $attribute, $data = [], ?\Closure $condition = null)
    {
        $key = $data[$this->localKey] ?? null;

        if ($key) {
            /**
             * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
             */
            $query = $repository->source()->builder($repository->entity(), $repository->mapping());
            $query->where($this->foreignKey, '=', $key);

            if ($condition) {
                $condition($query);
            }

            return $query->get();
        }

        return null;
    }
}