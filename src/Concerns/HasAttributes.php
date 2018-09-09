<?php

namespace Sprocketbox\Articulate\Concerns;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Components\Component;
use Sprocketbox\Articulate\Contracts\Attributeable;

/**
 * Trait HasAttributes
 *
 * @package Sprocketbox\Articulate\Concerns
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
     * @var array
     */
    private $_attributeables = [];

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
            $this->{$methodName}($value);
        } else {
            $this->setAttribute($attribute, $value);
        }
    }

    /**
     * @param string $attribute
     * @param        $value
     */
    public function setAttribute(string $attribute, $value): void
    {
        $attribute = snake_case($attribute);

        if ($this->getAttribute($attribute) !== $value) {
            $this->_dirty[]                = $attribute;
            $this->_attributes[$attribute] = $value;
        }
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
            return [$key => $this->get($key)];
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
        return $this->getAllAttributes(collect($this->_attributes))->toArray();
    }

    private function getAllAttributes(Collection $attributes)
    {
        return $attributes->map(function ($attribute) {
            if ($attribute instanceof Attributeable) {
                return $attribute->getAttributes();
            }

            if ($attribute instanceof Collection) {
                return $this->getAllAttributes($attribute);
            }

            return $attribute;
        });
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_attributes[snake_case($name)]);
    }

    /**
     * @param null|string $column
     *
     * @return bool
     */
    public function isDirty(?string $column = null): bool
    {
        $dirty = $column ? \in_array($column, $this->_dirty, true) : \count($this->_dirty) > 0;

        if ($dirty) {
            return true;
        }

        foreach ($this->_attributes as $key => $value) {
            if ($value instanceof Component && $value->isDirty($column)) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     */
    public function clean(): void
    {
        $this->_dirty = [];

        foreach ($this->_attributes as $key => $value) {
            if ($value instanceof Component) {
                $value->clean();
            }
        }
    }

    /**
     * Whether a offset exists
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return $this->get($offset) !== null;
    }

    /**
     * Offset to retrieve
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return self
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): self
    {
        $this->set($offset, $value);
        return $this;
    }

    /**
     * Offset to unset
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return self
     * @since 5.0.0
     */
    public function offsetUnset($offset): self
    {
        $this->set($offset, null);
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->getAll();
    }
}