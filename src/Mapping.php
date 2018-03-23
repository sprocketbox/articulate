<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Ollieread\Articulate\Concerns\MapsColumns;
use Ollieread\Articulate\Contracts\Mapping as Contract;

class Mapping implements Contract
{
    use Concerns\MapsColumns, Macroable {
        Macroable::__call as macroCall;
        MapsColumns::__call as columnCall;
    }

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

    public function __construct(string $entity, string $connection, ?string $table = null)
    {
        $this->entity     = $entity;
        $this->connection = $connection;
        $this->table      = $table;
        $this->columns    = new Collection;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|\Ollieread\Articulate\Contracts\Column
     * @throws \ReflectionException
     */
    public function __call($name, $arguments)
    {
        if (self::hasMacro($name)) {
            return $this->macroCall($name, $arguments);
        }

        return $this->columnCall($name, $arguments);
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
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepository(): ?string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     *
     * @return self
     */
    public function setRepository(string $repository): self
    {
        $this->repository = $repository;

        return $this;
    }
}