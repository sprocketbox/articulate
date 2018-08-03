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

# Dynamic & Computed
In theory, once you have an instance of entity, you can get and set as many attributes as you wish, though only those mapped
via the [mapper](/mappers) (and of those, only those that aren't marked as dynamic or immutable) will be persisted to the data source.

Dynamic attributes can either be set without a mapping, or if you wish to make use of attribute casting, you can use the `setDynamic()`
method on the attribute mapping.

Computed attributes aren't entirely that different to dynamic, except that they will typically return a value using one or more
of the existing attributes. If you had an entity with `title`, `first_name` and `last_name` attributes, you could add the following getter;

```php
public function getName(): string {
    return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
}
```

Now you could call `$entity->getName()` or even `$entity->name` rather than having to build up the name from its parts manually.
