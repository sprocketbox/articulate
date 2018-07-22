<?php

namespace Sprocketbox\Articulate\Attributes;

use Sprocketbox\Articulate\Contracts\Attribute;

/**
 * Class BaseColumn
 *
 * @package Sprocketbox\Articulate\Attributes
 */
abstract class BaseAttribute implements Attribute
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
     * @var array
     */
    protected $belongsTo = [];

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
    public function getName(): string
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
     * @return \Sprocketbox\Articulate\Attributes\BaseAttribute
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
     * @return BaseAttribute
     */
    public function setDefault($default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return \Sprocketbox\Articulate\Attributes\BaseAttribute
     */
    public function setAutoGenerate(): self
    {
        $this->auto = true;

        return $this;
    }

    /**
     * @param \Closure $generator
     *
     * @return \Sprocketbox\Articulate\Attributes\BaseAttribute
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

    public function isComponent(): bool
    {
        return false;
    }

    public function getComponent(): ?string
    {
        return null;
    }

    public function for(string ...$entities)
    {
        $this->belongsTo = $entities;
        return $this;
    }

    public function belongsTo(string $class): bool
    {
        return $this->belongsTo ? \in_array($class, $this->belongsTo, true) : true;
    }
}