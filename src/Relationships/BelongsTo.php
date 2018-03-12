<?php

namespace Ollieread\Articulate\Relationships;

use Illuminate\Support\Collection;
use Ollieread\Articulate\EntityManager;

class BelongsTo extends BaseRelationship
{
    public function load(&$results, array $children = [])
    {
        $manager = app(EntityManager::class);

        if (is_array($results)) {
            //Is array
        } else if ($results instanceof Collection) {
            $ids = $results->pluck($this->getForeignKey())
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
}