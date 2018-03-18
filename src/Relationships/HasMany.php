<?php

namespace Ollieread\Articulate\Relationships;

use Illuminate\Support\Collection;
use Ollieread\Articulate\EntityManager;

class HasMany extends BaseRelationship
{
    /**
     * @param       $results
     * @param array $children
     *
     * @throws \RuntimeException
     */
    public function load(Collection $results, array $children = [])
    {
        $manager = app(EntityManager::class);
        $ids     = $results->pluck($this->getLocalKey())
            ->filter(function ($id) {
                return ! empty($id);
            })->unique();
        $query   = $manager->newQueryBuilder($this->getRelationshipClass());
        $related = $query
            ->noHydrate()
            ->whereIn($this->getForeignKey(), $ids)
            ->get()
            ->groupBy(function ($item) {
                return $item->{$this->getForeignKey()};
            });

        $results->map(function (&$result) use ($manager, $related) {
            $result->{$this->getName()} = $related->get($result->{$this->getLocalKey()}, collect());

            return $result;
        });
    }
}