<?php

namespace Ollieread\Articulate\Columns;

use Ollieread\Articulate\Contracts\Column;

/**
 * Class BaseColumn
 *
 * @package Ollieread\Articulate\Columns
 */
abstract class BaseColumn implements Column
{
    /**
     * @var string
     */
    protected $columnName;

    /**
     * @var bool
     */
    protected $immutable = false;

    /**
     * @var bool
     */
    protected $dynamic = false;

    /**
     * BaseColumn constructor.
     *
     * @param string $columnName
     */
    public function __construct(string $columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     *
     */
    public function setImmutable()
    {
        $this->immutable = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    /**
     *
     */
    public function setDynamic()
    {
        $this->dynamic = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDynamic(): bool
    {
        return $this->dynamic;
    }
}