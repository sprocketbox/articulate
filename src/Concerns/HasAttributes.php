<?php

namespace Ollieread\Articulate\Concerns;

/**
 * Trait HasAttributes
 *
 * @package Ollieread\Articulate\Concerns
 */
trait HasAttributes
{
    /**
     * @var array
     */
    private $_attributes = [];

    /**
     * @var array
     */
    private $_dirty = [];

    /**
     * @param $name
     * @param $value
     *
     * @return null
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param string $attribute
     * @param        $value
     *
     * @return mixed
     */
    public function set(string $attribute, $value): void
    {
        $methodName = 'set' . studly_case($attribute);

        if (method_exists($this, $methodName)) {
            $this->{$methodName}();
        }

        $this->setAttribute($attribute, $value);
    }

    /**
     * @param string $attribute
     * @param        $value
     */
    public function setAttribute(string $attribute, $value): void
    {
        $attribute                     = snake_case($attribute);
        $this->_attributes[$attribute] = $value;
        $this->_dirty[]                = $attribute;
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get(string $attribute)
    {
        return $this->get($attribute);
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function get(string $attribute)
    {
        $methodName = 'get' . studly_case($attribute);
        if (method_exists($this, $methodName)) {

            return $this->{$methodName}();
        }

        return $this->getAttribute($attribute);
    }

    public function getAll(): array
    {
        return collect($this->_attributes)->mapWithKeys(function ($value, $key) {
            return $this->get($key);
        })->toArray();
    }

    /**
     * @param string $attribute
     *
     * @return null
     */
    public function getAttribute(string $attribute)
    {
        return $this->_attributes[snake_case($attribute)] ?? null;
    }

    public function getAttributes(): array
    {
        return $this->_attributes;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_attributes[$name]);
    }

    /**
     * @param null|string $column
     *
     * @return bool
     */
    public function isDirty(?string $column = null): bool
    {
        return $column ? \in_array($column, $this->_dirty, true) : \count($this->_dirty) > 0;
    }

    /**
     *
     */
    public function clean(): void
    {
        $this->_dirty = [];
    }
}