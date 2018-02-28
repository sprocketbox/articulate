<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Relationships\BelongsTo;

class Mapper
{
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

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $relationships;

    public function __construct(string $entity, string $connection, string $table, ?string $key = null)
    {
        $this->entity        = $entity;
        $this->connection    = $connection;
        $this->table         = $table;
        $this->key           = $key;
        $this->relationships = new Collection;
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
     * @return \Ollieread\Articulate\Mapper
     */
    public function setKey(string $key): Mapper
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
     * @return Mapper
     */
    public function setRepository(string $repository): Mapper
    {
        $this->repository = $repository;

        return $this;
    }

    public function belongsTo(string $entity, string $foreignKey)
    {
        $this->relationships->put($entity, new BelongsTo($this->entity, $entity, $foreignKey));
    }
}