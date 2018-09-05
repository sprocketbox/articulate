<?php

namespace Sprocketbox\Articulate\Sources\Illuminate;

use Sprocketbox\Articulate\Entities\EntityMapping;

class IlluminateEntityMapping extends EntityMapping
{
    /**
     * @var string|null
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @return null|string
     */
    public function getConnection(): ?string
    {
        return $this->connection;
    }

    /**
     * @param null|string $connection
     *
     * @return $this
     */
    public function setConnection(?string $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }
}