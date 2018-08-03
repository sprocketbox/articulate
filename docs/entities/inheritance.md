There may be cases where you have multiple types of an entity, all defined by a single dataset. For example, you may have
a `type` column, and depending on the value of this column a certain subset of available data may be returned.

Since Articulate is datasource agnostic there isn't the concept of Single Table Inheritance or Multiple Table Inheritance, 
but simply Multiple Inheritance. This is performed by providing a logical case in your entities mapper, and mapping
certain attributes to only be available for child entities.

All child entities should extend your parent entity, which is the entity that the mapper specifies. Outside of extending
the parent entity, all multiple inheritance is defined and controlled by the [mapper](mappers.md#multiple-inheritance)