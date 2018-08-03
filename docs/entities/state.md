An entity has two states, persisted, and dirty.

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