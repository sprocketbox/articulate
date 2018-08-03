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

