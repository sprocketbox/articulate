<?php

namespace Sprocketbox\Articulate\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Contracts\ComponentMapping as Contract;

class ComponentMapping implements Contract
{
    use Concerns\MapsAttributes,
        Macroable {
        Macroable::__call as macroCall;
        Concerns\MapsAttributes::__call as attributeCall;
    }

    /**
     * @var string
     */
    protected $component;

    public function __construct(string $component)
    {
        $this->component  = $component;
        $this->attributes = new Collection;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|\Sprocketbox\Articulate\Contracts\Attribute
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (self::hasMacro($name)) {
            return $this->macroCall($name, $arguments);
        }

        return $this->attributeCall($name, $arguments);
    }

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    public function make(...$arguments)
    {
        $entityClass = $this->getComponent();
        return new $entityClass(...$arguments);
    }
}