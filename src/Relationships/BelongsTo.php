<?php

namespace Ollieread\Articulate\Relationships;

use Illuminate\Support\Collection;
use Ollieread\Articulate\EntityManager;

class BelongsTo extends BaseRelationship
{
    /**
     * @param \Illuminate\Support\Collection $results
     * @param array                          $children
     *
     * @throws \RuntimeException
     */
    public function load(Collection $results, array $children = [])
    {
        $manager = app(EntityManager::class);
        $ids     = $results->pluck($this->getForeignKey())
            ->filter(function ($id) {
                return ! empty($id);
            })->unique();

        $query   = $manager->newQueryBuilder($this->getRelationshipClass());
        $related = $query
            ->whereIn($this->getLocalKey(), $ids)
            ->get()
            ->keyBy(function ($item) {
                return $item->{$this->getLocalKey()};
            });

        $results->map(function (&$result) use ($manager, $related) {
            $result->{$this->getName()} = $related->get($result->{$this->getForeignKey()}, null);

            return $result;
        });
    }
}