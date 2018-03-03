<?php

namespace Ollieread\Articulate\Columns;

use Ollieread\Articulate\Contracts\Column;

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

    public function __construct(string $columnName)
    {
        $this->columnName = $columnName;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function setImmutable()
    {
        $this->immutable = true;
    }

    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    public function setDynamic()
    {
        $this->dynamic = true;
    }

    public function isDynamic(): bool
    {
        return $this->dynamic;
    }
}