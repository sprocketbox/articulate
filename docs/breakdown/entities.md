Entities could be considered Articulates answer to models, but in essence, they're nothing more than simple data objects.
Each entity represents a resource, and contains a particular part of a dataset.

All of Articulates entities extend the following class;

    Sprocketbox\Articulate\Entities\Entity
    
This class by default provides attribute handling, as well as a few magic methods so that your data can be accessed
as entity properties. A fully functioning example entity would be as follows;

```php
namespace App\Entities;

use Sprocketbox\Articulate\Entities\Entity;

class MyEntity extends Entity {
}
```

All of the attributes are provided by the [mapper](/mappers) and generated [mapping](/mapping) so nothing more is needed.
Since most IDEs have problems with magic properties, it is always worth adding a docblock to help it out;

```php
namespace App\Entities;

use Sprocketbox\Articulate\Entities\Entity;

/**
 * Class MyEntity
 *
 * @property-read int       $id
 * @property string         $name
 * @property string         $description
 * @property \Carbon\Carbon $createdAt
 * @property \Carbon\Carbon $updatedAt
 *
 * @package App\Entities
 */
class MyEntity extends Entity {
}
```

# Attributes

There are several ways for you to interact with your entities attributes.

## Magic Properties
By default, all attributes are available as magic properties, so you don't need to do anything special to get them working.

Internally, the code handling magic properties will hook into any getters and or setters you happen to create.

## Getters & Setters
If you wish to create getters or setters for particular attributes, you can do so by StudlyCasing the attribute names, and
prefixed with `get` or `set`, like below;

```php
public function getName(): string {
    return $this->getAttribute('name');
}

public function setName(string $name): self {
    return $this->>setAttribute('name', $name);
}

public function getCreatedAt(): Carbon {
    return $this->getAttribute('created_at');
}

public function setCreatedAt(Carbon $createdAt): self {
    return $this->>setAttribute('created_at', $createdAt);
}
```

If you wish to get or set an attribute on entity, but don't want to actually call the getter or setter, you can use the
`get` and `set` methods like so;

```php
$entity->get('name');
$entity->set('name', $name);
$entity->get('created_at');
$entity->set('created_at', $createdAt);
```

This methods will check for the existence of getter and/or setter, before defaulting the next method. It is important to not use
these methods inside your getters and setters, otherwise you'll create an nice little infinite loop.

## Manual
If you wish to manually set or get an attribute, skipping and getters or setters, you can use the `getAttribute` and `setAttribute`
methods like so;

```php
$entity->getAttribute('name');
$entity->setAttribute('name', $name);
$entity->getAttribute('created_at');
$entity->setAttribute('created_at', $createdAt);
```

# Dynamic & Computed Attributes
In theory, once you have an instance of entity, you can get and set as many attributes as you wish, though only those mapped
via the [mapper](/mappers) (and of those, only those that aren't marked as dynamic or immutable) will be persisted to the data source.

## Dynamic Attributes
Dynamic attributes can either be set without a mapping, or if you wish to make use of attribute casting, you can use the `setDynamic()`
method on the attribute mapping.

## Computed Attributes
Computed attributes aren't entirely that different to dynamic, except that they will typically return a value using one or more
of the existing attributes. If you had an entity with `title`, `first_name` and `last_name` attributes, you could add the following getter;

```php
public function getName(): string {
    return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
}
```

Now you could call `$entity->getName()` or even `$entity->name` rather than having to build up the name from its parts manually.

# State
An entity has three states, persisted, not persisted and dirty.

## Persisted State
All entities have a persisted state, which can be checked by calling `isPersisted()` which returns a boolean. If for some reason you wanted
to set an entity as persisted manually, you could also call `setPersisted()`.

An entity with a persisted state would typically cause a source to update the resource, where as a none
persisted entity would cause it to create the resource.

## Dirty State
Entities store the state of each attribute, so that when you set one, it is marked as dirty. This can be used by the
sources so that they do not attempt to persist an entity that hasn't changed.

You can check whether an entity is dirty by calling `isDirty()`, and check whether a particular attribute is dirty by calling
`isDirty('attribute_name')`, both of which return a boolean.