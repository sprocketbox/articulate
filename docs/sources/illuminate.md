The Illuminate source allows for entities to use the default query builder that ships with Laravel as their data source.

To use this source, your entity mapper should return `illuminate` as its source, like so;

```php
public function source(): string {
    return 'illuminate';
}
```

# Mapping
Entities that use this source will have the following mapping injected into their mapper;

    Sprocketbox\Articulate\Sources\Illuminate\IlluminateEntityMapping

## Setting the Table
To set the table of the entity you can call the `setTable(string $table)` method like so;

```php
$mapping->setTable('my_entities');
```

This is a required setting.

## Setting the Connection
To set the connection of the entity you can call the `setConnection(string $connection)` method like so;

```php
$mapping->setConnection('mysql');
```

The value of this method must match up with a defined connection in the `database.connections` configuration. If you wish
to use the default connection defined by `database.default` you can skip this.

# Builder
The Illuminate builder is a small piece of abstraction that wraps the default query builder. This builder is;

    Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder
    
You can treat this the same way you'd treat the normal query builder, as any methods that are not overwritten are proxied
through to an internal instance of;

    Illuminate\Database\Query\Builder

# Repositories
Repositories for Illuminate entities should extends the following class;

    Sprocketbox\Articulate\Sources\Illuminate\IlluminateRepository
    
## Querying
To create a new instance of the [builder](#builder) you can call the `query(?string $entity = null)` method. This will return a new builder
set to the table of the entity the repository is currently configured for. Passing in another entities class in here will
create a new instance of the builder for that entity.

You can also create [critera](/breakdown/repositories#criteria) using the `getOneBy(Criteria... $criteria)` and the `getBy(Criteria.. $criteria)`
methods, which will return a hydrated entity and a collection of hydrated entities, respectively.

By default the query builder will not run criteria (outside of the above `getOneBy` and `getBy` methods), so to manually do this you'll
need to call `applyCriteria(Builder $query)` which return the query so that you can chain.

## Deleting
To delete an entity, simply pass it into the `delete(Entity $entity)` method.

At the time of writing, soft deletes are not supported natively, but you could override this method and use criteria
to recreate it.

## Paginating
To paginate the result of a query, you can pass in a builder instance to `paginate($query, int $count, string $pageName = 'page')`.

## Saving
To save an entity, or persist it to the database, just call `save(Entity $entity)`.