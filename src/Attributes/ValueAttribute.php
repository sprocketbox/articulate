<?php

namespace Sprocketbox\Articulate\Attributes;

class ValueAttribute extends BaseAttribute
{
    /**
     * @var string
     */
    protected $valueClass;

    /**
     * @var array 
     */
    protected $arguments;

    /**
     * EntityColumn constructor.
     *
     * @param string $attributeName
     * @param string $valueClass
     * @param array  $arguments
     */
    public function __construct(string $attributeName, string $valueClass, ...$arguments)
    {
        parent::__construct($attributeName);
        $this->valueClass = $valueClass;
        $this->arguments  = $arguments;
    }

    public function cast($value, array $data = [])
    {
        if (! $value || $value instanceof $this->valueClass) {
            return $value;
        }

        $valueClass = $this->valueClass;
        
        return new $valueClass($value, ...$this->arguments);
    }

    public function parse($value, array $data = [])
    {
        if (! ($value instanceof $this->valueClass)) {
            return $value;
        }

        return null;
    }
}