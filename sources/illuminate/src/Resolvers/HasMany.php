<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

use Illuminate\Support\Collection;

class HasMany extends HasOne
{

    public function getRelated(string $attribute, Collection $data, ?\Closure $condition = null): ?Collection
    {
        $keys = $data->map(function ($row) {
            return $row[$this->localKey] ?? null;
        })->filter(function ($key) {
            return ! empty($key);
        });

        if ($keys->count() > 0) {
            $repository = entities()->repository($this->relatedEntity);

            if ($repository) {
                /**
                 * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
                 */
                $query = $repository->source()->builder($repository->entity(), $repository->mapping());

                if ($keys->count() > 1) {
                    $query->whereIn($this->relatedKey, $keys->toArray());
                } else {
                    $query->where($this->relatedKey, '=', $keys->first());
                }

                if ($condition) {
                    $condition($query);
                }

                $results = $query->get()->groupBy($this->relatedKey);

                return $data->map(function ($row) use ($results, $attribute) {
                    $row[$attribute] = $results->get($row[$this->localKey]);
                    return $row;
                });
            }
        }

        return null;
    }
}