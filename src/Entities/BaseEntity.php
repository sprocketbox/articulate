<?php

namespace Ollieread\Articulate\Entities;

abstract class BaseEntity
{
    /**
     * @var array
     */
    private $_attributes = [];

    /**
     * @var array
     */
    private $_dirty = [];

    public function set(string $attribute, $value)
    {
        $this->_attributes[$attribute] = $value;
        $this->_dirty[]                = $attribute;
    }

    public function get(string $attribute)
    {
        return $this->_attributes[$attribute] ?? null;
    }

    public function __get(string $attribute)
    {
        return $this->get(snake_case($attribute));
    }

    public function isDirty(?string $column = null): bool
    {
        return $column ? in_array($column, $this->_dirty) : count($this->_dirty) > 0;
    }

    public function clean()
    {
        $this->_dirty = [];
    }
}