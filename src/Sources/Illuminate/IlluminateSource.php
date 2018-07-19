<?php

namespace Sprocketbox\Articulate\Sources\Illuminate;

use Illuminate\Database\DatabaseManager;
use Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping;
use Sprocketbox\Articulate\Contracts\Source;

class IlluminateSource implements Source
{
    /**
     * @param string $entity
     *
     * @return \Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping
     */
    public function newMapping(string $entity, string $source): IlluminateEntityMapping
    {
        return new IlluminateEntityMapping($entity, $source);
    }

    public function builder(...$arguments)
    {
        /**
         * @var null|string                                                        $entity
         * @var \Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping $mapping
         * @var \Illuminate\Database\DatabaseManager                               $database
         * @var \Illuminate\Database\Query\Builder                                 $query
         */
        [$entity, $mapping] = $arguments;
        $illuminate = app(DatabaseManager::class);
        $query      = $illuminate->connection($mapping->getConnection())->query();
        $entity     = $entity ?? $mapping->getEntity();

        if ($entity) {
            $query->from($mapping->getTable());
        }

        return (new IlluminateBuilder($query, entities()))->setEntity($entity ?? $mapping->getEntity());
    }
}