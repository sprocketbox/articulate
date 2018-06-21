<?php

namespace Ollieread\Articulate\Relationships;

use Ollieread\Articulate\Columns\EntityColumn;
use Ollieread\Articulate\Criteria\WhereCriteria;
use Ollieread\Articulate\Support\Collection;

class HasMany extends Relationship
{
    /**
     * @var string
     */
    protected $foreignEntity;

    /**
     * @var string
     */
    protected $columnName;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $localKey;

    /**
     * @var \Closure
     */
    protected $customLoad;

    public function __construct(string $foreignEntity, string $columnName, string $foreignKey, string $localKey)
    {
        $this->foreignEntity = $foreignEntity;
        $this->columnName    = $columnName;
        $this->foreignKey    = $foreignKey;
        $this->localKey      = $localKey;
    }

    public function setLoad(\Closure $customLoad)
    {
        $this->customLoad = $customLoad;
    }

    public function getColumn()
    {
        return new EntityColumn($this->columnName, $this->foreignEntity, true);
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function load($key)
    {
        if ($this->customLoad) {
            return \call_user_func($this->customLoad, $key);
        }

        /**
         * @var \Ollieread\Articulate\Repositories\DatabaseRepository $repository
         */
        $repository = entities()->repository($this->foreignEntity);

        if ($repository) {
            return $repository->getByCriteria(new WhereCriteria($this->localKey, '=', $key));
        }

        return new Collection;
    }
}