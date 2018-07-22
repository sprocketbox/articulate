<?php

namespace Sprocketbox\Articulate\Concerns;

use Illuminate\Support\Collection;
use Sprocketbox\Articulate\Attributes;
use Sprocketbox\Articulate\Contracts\Attribute;
use RuntimeException;

/**
 * Trait MapsAttributes
 *
 * @method Attributes\BoolAttribute bool(string $attributeName)
 * @method Attributes\EntityAttribute entity(string $attributeName, string $entityClass, bool $multiple = false)
 * @method Attributes\IntAttribute int(string $attributeName)
 * @method Attributes\JsonAttribute json(string $attributeName)
 * @method Attributes\StringAttribute string(string $attributeName)
 * @method Attributes\TextAttribute text(string $attributeName)
 * @method Attributes\ArrayAttribute array(string $attributeName)
 * @method Attributes\ComponentAttribute component(string $attributeName, string $componentClass))
 * @method Attributes\TimestampAttribute timestamp(string $attributeName, string $format = 'Y-m-d H:i:s')
 * @method Attributes\FloatAttribute float(string $attributeName)
 * @method Attributes\UuidAttribute uuid(string $attributeName)
 *
 * The following methods are for MongoDB only
 *
 * @method Attributes\MongoDB\ObjectIdColumn objectId(string $attributeName)
 * @method Attributes\MongoDB\SubdocumentColumn subdocument(string $attributeName, string $entityClass, bool $multiple = false)
 * @method Attributes\MongoDB\UtcColumn utc(string $attributeName)
 *
 * @package Sprocketbox\Articulate\Concerns
 */
trait MapsAttributes
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * @param $name
     * @param $arguments
     *
     * @return \Sprocketbox\Articulate\Contracts\Attribute
     * @throws \RuntimeException
     */
    public function __call($name, $arguments)
    {
        $attribute      = snake_case($name);
        $attributeClass = config('articulate.attributes.' . $attribute, null);

        if ($attributeClass && class_exists($attributeClass)) {
            return $this->newAttribute(config('articulate.attributes.' . $attribute), $arguments);
        }

        throw new RuntimeException(sprintf('Invalid attribute %s', $attribute));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Attribute $type
     *
     * @return \Sprocketbox\Articulate\Contracts\Attribute
     */
    public function mapAttribute(Attribute $type): Attribute
    {
        $this->attributes->put($type->getName(), $type);

        return $type;
    }

    /**
     * @param string $attribute
     *
     * @return null|\Sprocketbox\Articulate\Contracts\Attribute
     */
    public function getAttribute(string $attribute): ?Attribute
    {
        $attributeName = $attribute;

        return $this->attributes->first(function (Attribute $attribute) use ($attributeName) {
            return $attribute->getName() === $attributeName || $attribute->getColumnName() === $attributeName;
        });
    }

    /**
     * @param string $attributeClass
     * @param        $arguments
     *
     * @return \Sprocketbox\Articulate\Contracts\Attribute
     * @throws \InvalidArgumentException
     */
    protected function newAttribute(string $attributeClass, $arguments): Attribute
    {
        return $this->mapAttribute(new $attributeClass(...$arguments));
    }

    public function timestamps()
    {
        $this->timestamp('created_at');
        $this->timestamp('updated_at');
    }

    public function utcTimestamps()
    {
        $this->utc('created_at');
        $this->utc('updated_at');
    }
}