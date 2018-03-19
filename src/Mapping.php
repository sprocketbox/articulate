<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Ollieread\Articulate\Relationships\BelongsTo;

class Mapping
{
    use Macroable,
        Concerns\MapsColumns,
        Concerns\MapsRelationships;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $repository;

    public function __construct(string $entity, string $connection, string $table)
    {
        $this->entity        = $entity;
        $this->connection    = $connection;
        $this->table         = $table;
        $this->relationships = new Collection;
        $this->columns       = new Collection;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return \Ollieread\Articulate\Mapping
     */
    public function setKey(string $key): Mapping
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     *
     * @return Mapping
     */
    public function setRepository(string $repository): Mapping
    {
        $this->repository = $repository;

        return $this;
    }
}