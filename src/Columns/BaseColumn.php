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
    protected $attributeName;

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
     * @var bool
     */
    protected $auto = false;

    /**
     * @var null|\Closure
     */
    protected $generator;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * BaseColumn constructor.
     *
     * @param string $attributeName
     */
    public function __construct(string $attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attributeName;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName ?? $this->attributeName;
    }

    /**
     * @param string $columnName
     *
     * @return \Ollieread\Articulate\Columns\BaseColumn
     */
    public function setColumnName(string $columnName): self
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     *
     */
    public function setImmutable(): self
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
    public function setDynamic(): self
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

    /**
     * @return mixed
     */
    public function getDefault()
    {
        $default = $this->default;

        return $default instanceof \Closure ? $default() : $default;
    }

    /**
     * @param mixed $default
     *
     * @return BaseColumn
     */
    public function setDefault($default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return \Ollieread\Articulate\Columns\BaseColumn
     */
    public function setAutoGenerate(): self
    {
        $this->auto = true;

        return $this;
    }

    /**
     * @param \Closure $generator
     *
     * @return \Ollieread\Articulate\Columns\BaseColumn
     */
    public function setGenerator(\Closure $generator): self
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return mixed|null
     */
    public function generate(array $attributes)
    {
        if ($this->auto && $this->generator) {
            $generator = $this->generator;

            return $generator($attributes);
        }

        return null;
    }
}