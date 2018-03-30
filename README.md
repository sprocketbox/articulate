# Articulate #

[![Latest Stable Version](https://poser.pugx.org/ollieread/articulate/v/stable.png)](https://packagist.org/packages/ollieread/articulate) [![Total Downloads](https://poser.pugx.org/ollieread/articulate/downloads.png)](https://packagist.org/packages/ollieread/articulate) [![Latest Unstable Version](https://poser.pugx.org/ollieread/articulate/v/unstable.png)](https://packagist.org/packages/ollieread/articulate) [![License](https://poser.pugx.org/ollieread/articulate/license.png)](https://packagist.org/packages/ollieread/articulate)

- **Laravel**: 5.5, 5.6
- **PHP**: 7.2+
- **Author**: Ollie Read 
- **Author Homepage**: http://ollieread.com

## What is Articulate?

Articulate is a data source agnostic entity mapper.

## Why Articulate?

Traditional ORMs have a lot of magic, and introduce their own limitations or specific ways of working.
It doesn't take long before you need to do something that your ORM of choice doesn't do, and you have
to find a way to perform your given task, within their confines. 

Articulates entities would be domain specific and their resemblance to the database is entirely down to
your own preferences. Structure your data using entities, without the overhead of passing around the 
entire DBAL.

## Installation

Package is available on [Packagist](https://packagist.org/packages/ollieread/articulate), you can install it using Composer.

    composer require ollieread/articulate
    
Next you'll want to publish the configuration.

    php artisan vendor:publish --provider=Ollieread\Articulate\ServiceProvider
    
## Configuration

The configuration file will be located at `config/articulate.php`.

There are three config options;

### articulate.mappings 

This is an array of all mappings. For example;

    'mappings' => [
        \App\Mappings\TestMapping::class,
    ],
    
### articulate.columns

This is an array of columns. The default column definitions are already in there. For example;

    'columns' => [
        'bool'      => \Ollieread\Articulate\Columns\BoolColumn::class,
        'entity'    => \Ollieread\Articulate\Columns\EntityColumn::class,
        'int'       => \Ollieread\Articulate\Columns\IntColumn::class,
        'json'      => \Ollieread\Articulate\Columns\JsonColumn::class,
        'string'    => \Ollieread\Articulate\Columns\StringColumn::class,
        'timestamp' => \Ollieread\Articulate\Columns\TimestampColumn::class,
    ],
    
### articulate.auth

A simple `true` or `false` telling Articulate to register the auth provider.
    
## Usage

You can use the entity manager by injecting the following class;

    Ollieread\Articulate\EntityManager
    
There is no facade for this class, as I do not like facades.

### Entities

Entities are basic value key => value stores and represent a row in your database. All entities should extend the following class;

    Ollieread\Articulate\Entities\BaseEntity
    
Or implement the following contract;

    Ollieread\Articulate\Contracts\Entity

An entity should have a getter and a setter for the columns in the database. 

The method name should match the column name in studly case, with getters being prefixed with `get` and setters with `set`. For example;

    class Test extends BaseEntity
    {        
        public function getId(): int
        {
            return $this->get('id');
        }
        
        public function setId(int $id): Test
        {
            $this->set('id', $id);
    
            return $this;
        }
        
        public function getName(): string
        {
            return $this->get('name');
        }
        
        public function setName(string $name): Test
        {
            $this->set('name', $name);
    
            return $this;
        }
    }
    
The BaseEntity keeps track of the entities attributes, as well as whether or not a particular attribute is dirty. This allows you to have dynamic attributes as well as add custom attributes that shouldn't be persisted to the database.

It also provides a global setter and getter.

The BaseEntity class also contains a `__get(string $attribute)` implementation so you can access attribute as if they were properties. To help with IDE implementation, I suggest that you use the `@property-ready type $attribute` helpers to your entity class doc block. Like the following;

    /**
     * Class Test
     *
     * @property-read int                        $id
     * @property-read string                     $name
     * @property-read string                     $description
     * @property-read \Illuminate\Support\Carbon $createdAt
     * @property-read \Illuminate\Support\Carbon $updatedAt
     *
     * @package App\Entities
     */
    
#### Setters

Your setters should call the `set(string $column, mixed $value)` method from the BaseEntity.

#### Getters

Your getters should call the `get(string $column)` method from the BaseEntity;
    
### Mappings

Mappings handle the mapping between the entity and its table, connection, repository and even other entities.

Mappings can either implement the following;

    Ollieread\Articulate\Contracts\Mapping
    
Or extend the following;

    Ollieread\Articulate\EntityMapping
    
A mapping has four methods.

#### entity(): string

This method should return the name of the entity class, including the full namespace. For example;

    public function entity(): string
    {
        return App\Entities\Test::class;
    }
    
#### table(): string

This method should return the name of the table that this entity represents. For example;

    public function table(): string
    {
        return 'tests';
    }
    
#### connection(): ?string

This method should return the name of the connection for this entity. You can return `null` or empty if you wish to use the default. If you extend the `EntityMapping` class, you do not need to define this if you do not wish to.

#### map(Mapper $mapper)

This method will receive an instance of the `Mapper` class, which stores the processed mapping of an entity.
In here you would define characteristics of the entity. Avalable options are;

Method | Description |
:-------|:----|
`setKey(string $key)` | Defines which column is the primary key |
`setRepository(string $repository)` | Defines that class that should be used as this entities repository |
`mapColumn(Ollieread\Articulate\Contracts\Column $column)` | Defines the type of column and returns the instance passed in |

An example implementation would be as follows;

    public function map(Mapper $mapper)
    {
        $mapper->setKey('id');
        $mapper->setRepository(TestRepository::class);
        $mapper->mapColumn(new IntColumn('id'))->setImmutable();
        $mapper->mapColumn(new StringColumn('name'));
        $mapper->mapColumn(new StringColumn('description'));
        $mapper->mapColumn(new TimestampColumn('created_at'));
        $mapper->mapColumn(new TimestampColumn('updated_at'));
    }
    
It should be noted that you can map columns using helper methods. The helper method is always `map{type}(..)` where `{type}` is the key
used in the `articulate.columns` config mapping, in studly caps. For example;

    $mapper->mapString('name');
    $mapper->mapTimestamp('created_at', 'Y-m-d H:i:s');
    
#### Column Mapping

Column mapping is used so that the builder hydration knows which columns belong to which entity, as well as allowing database returns to be cast to the correct value.

You can create your own column mapping class providing that it extends the following class;

    Ollieread\Articulate\Contracts\Column
    
The available column mappings are as follows;

Class | Description |
:-------|:----|
`Ollieread\Articulate\Columns\IntColumn` | Define the column as an integer |
`Ollieread\Articulate\Columns\StringColumn` | Define the column as a string |
`Ollieread\Articulate\Columns\TimestampColumn` | Define the column as a timestamp |
`Ollieread\Articulate\Columns\BoolColumn` | Define the column as a boolean |
`Ollieread\Articulate\Columns\JsonColumn` | Define the column as a json string |
`Ollieread\Articulate\Columns\EntityColumn` | Define the column as another entity (performs hydration) |
`Ollieread\Articulate\Columns\FloatColumn` | Define the column as a float (`DECIMAL()` for MySQL) |

There are also the following MongoDB specific columns that are commented out in the config by default

Class | Description |
:-------|:----|
`Ollieread\Articulate\Columns\MongoDB\ObjectIdColumn` | Define the column as a MongoDB id (`_id` by default) |
`Ollieread\Articulate\Columns\MongoDB\SubdocumentColumn` | Define the column as a MongoDB subdocument |
`Ollieread\Articulate\Columns\MongoDB\UtcColumn` | Define the column as a timestamp for MongoDB |

You can map columns as dynamic using `setDynamic()`. The will prevent the value from being synced to the database. An example of a dynamic column would be the result of an expression in an SQL query or something generated in the code, that doesn't necessarily have a place in the database.

You can also map columns as being immutable using `setImmutable()`. Immutable columns can only be set during the initial hydrating of the entity, and like the dynamic, are ignored when it comes to syncing with the database.

All columns also have the option to provide a default value using `setDefault($default)`.

The `EntityColumn` also has a `setLoad(bool $load)` method. Setting this to true will call the `load($id)` method
present on its mapped repository in the case that a scalar value is passed into the cast method. This is
for belongs to relationships.

### Repositories

Repositories are how we retrieve instances of our entities. If you're familiar with the repository pattern, you'll recognise this particular way of doing things.

All user defined repositories should extend the following class;

    Ollieread\Articulate\Repositories\EntityRepository
    
Within here you may define your own methods as you see fit.

#### Laravel Query Builder

If you wish to use the default laravel query builder, an abstract repository has been provided for you to extend;

    Ollieread\Articulate\Repositories\DatabaseRepository
    
To create a new instance of the query builder for the entities connection, call the following;

    $this->query(?string $entityClass);

If you omit the `$entityClass` argument, the entity for the repository will be used.
    
You will need to tell Articulate to hydrate your entities, but that can be done by doing one of the following;

    $this->hydrate($results, ?string $entityClass);

If you omit the `$entityClass` argument, the entity for the repository will be used.
    
The above is available within a repository that extends the DatabaseRepository.

    $this->manager()->hydrate(string $entityClass, $row, $persisted = true);

Call the hydrate method on the EntityManager.

An example method within a repository would be as follows;

    public function getAll(bool $activeOnly = false) {
        $query = $this->query()
            ->select(['id', 'name', 'slug', 'created_at', 'updated_at'])
            ->from($this->mapping()->getTable())
            ->orderBy('created_at', 'desc');

        if ($activeOnly) {
            $query->where('active', '=', 1);
        }

        $results = $query->get();

        if ($results) {
            return $this->hydrate($results);
        }
        
        return new Collection;
    }
    
If you find yourself with an entity that needs to be array, you can dehyrdate it as follows;

    $this->manager()->dehydrate(Entity $entity): array;
    
This method with dehydrate to an array of the mapped columns. Any data in there not mapped, with not be present in the returned array.

##### Persisting  

If you have an entity that you have manually populated, you can persist it to the database by using the following;

    $entityRepository->save($entity);
    
The repository being used, must be the repository for that specific entity.

##### Pagination    

If you wish to use pagination, there is a helper method available;

    $this->paginate(Builder $query, int $count, string $pageName = 'page')
    
This will return an instance of;

    Illuminate\Contracts\Pagination\LengthAwarePaginator
    
The initial argument for this method should be the query you wish to run pagination on. An example follows;

    public function getPaginated(int $count, array $filters = []): LengthAwarePaginator {
        $query = $this->query($this->entity())->orderBy('post_at', 'desc');
        $this->processFilters($query, $filters);

        return $this->paginate($query, $count);
    }

### Migrations

Migrations are exactly the same, this package doesn't add anything specific regarding that.

### Authentication

If you wish to use Articulate for authentication you'll need to set the `articulate.auth` config option to true. 
Once this is done you should implement the following contract on the entity;

    Illuminate\Contracts\Auth\Authenticatable
    
You chosen entity will need a repository that implements the following;

    Ollieread\Articulate\Contracts\EntityAuthRepository
    
Now you can set your auth provider driver to `articulate`.

For those curious, the articulate user provider simply passes through to your repository of choice, 
giving you far more control over the auth process.

