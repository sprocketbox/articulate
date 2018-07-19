<?php

namespace Sprocketbox\Articulate\Attributes;

class ComponentAttribute extends BaseAttribute
{
    /**
     * @var string
     */
    protected $componentClass;

    /**
     * EntityColumn constructor.
     *
     * @param string $attributeName
     * @param string $componentClass
     */
    public function __construct(string $attributeName, string $componentClass)
    {
        parent::__construct($attributeName);
        $this->componentClass = $componentClass;
    }

    public function cast($value)
    {
        if (! $value || $value instanceof $this->componentClass) {
            return $value;
        }


    }

    public function parse($value)
    {
        // TODO: Implement parse() method.
    }

    public function isComponent(): bool
    {
        return true;
    }

    public function getComponent(): ?string
    {
        return $this->componentClass;
    }
}