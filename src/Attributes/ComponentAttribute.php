<?php

namespace Sprocketbox\Articulate\Attributes;

use Sprocketbox\Articulate\Components\ComponentMapping;

class ComponentAttribute extends BaseAttribute
{
    /**
     * @var string
     */
    protected $componentClass;

    /**
     * @var null|\Closure
     */
    protected $customMapping;

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

    public function cast($value, array $data = [])
    {
        return entities()->hydrateComponent($this->getComponent(), $this->getCustomMapping(), $value);
    }

    public function parse($value, array $data = [])
    {
        return entities()->dehydrateComponent($value);
    }

    public function isComponent(): bool
    {
        return true;
    }

    public function getComponent(): ?string
    {
        return $this->componentClass;
    }

    public function setCustomMapping($mapping)
    {
        $this->customMapping = $mapping;
        return $this;
    }

    public function getCustomMapping()
    {
        if ($this->customMapping) {
            $mapping       = new ComponentMapping($this->componentClass);
            $customMapping = $this->customMapping;
            $customMapping($mapping);

            return $mapping;
        }

        return null;
    }
}