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
     * @param string $attribute
     * @param        $value
     */
    public function set(string $attribute, $value): void
    {
        $this->_attributes[$attribute] = $value;
        $this->_dirty[]                = $attribute;
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function get(string $attribute)
    {
        return $this->_attributes[$attribute] ?? null;
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get(string $attribute)
    {
        return $this->get(snake_case($attribute));
    }

    /**
     * @param $name
     * @param $value
     *
     * @return null
     */
    public function __set($name, $value)
    {
        return null;
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