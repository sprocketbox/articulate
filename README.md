# Articulate #

[![Latest Stable Version](https://poser.pugx.org/ollieread/articulate/v/stable.png)](https://packagist.org/packages/ollieread/articulate) [![Total Downloads](https://poser.pugx.org/ollieread/articulate/downloads.png)](https://packagist.org/packages/ollieread/articulate) [![Latest Unstable Version](https://poser.pugx.org/ollieread/articulate/v/unstable.png)](https://packagist.org/packages/ollieread/articulate) [![License](https://poser.pugx.org/ollieread/articulate/license.png)](https://packagist.org/packages/ollieread/articulate)

- **Laravel**: 5.5, 5.6
- **PHP**: 7.2+
- **Author**: Ollie Read 
- **Author Homepage**: http://ollieread.com

## What is Articulate?

Articulate is an entity mapper package with little to no database knowledge. How exactly you hydrate the
entities is entirely up to you.

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

It has a single config option, and that is `articulate.mappings`. This is an array of all mappings. For example;

    'mappings' => [
        \App\Mappings\TestMapping::class,
    ],
    
## Usage

You can use the entity manager by using the following facade;

    Ollieread\Articulate\Facades\EntityManager
    
Or you can inject the following manager;

    Ollieread\Articulate\EntityManager

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
     * @property-read \Illuminate\Support\Carbon $created_at
     * @property-read \Illuminate\Support\Carbon $updated_at
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
`Ollieread\Articulate\Columns\EntityColumn` | Define the column as another entity (performs hydration) |

You can map columns as dynamic using `setDynamic()`. The will prevent the value from being synced to the database. An example of a dynamic column would be the result of an expression in an SQL query or something generated in the code, that doesn't necessarily have a place in the database.

You can also map columns as being immutable using `setImmutable()`. Immutable columns can only be set during the initial hydrating of the entity, and like the dynamic, are ignored when it comes to syncing with the database.

### Repositories

Repositories are how we retrieve instances of our entities. If you're familiar with the repository pattern, you'll recognise this particular way of doing things.

All user defined repositories should extend the following class;

    Ollieread\Articulate\Repositories\EntityRepository
    
Within here you may define your own methods as you see fit.

If you wish to use the default laravel query builder, an abstract repository has been provided for you to extend;

    Ollieread\Articulate\Repositories\DatabaseRepository
    
To create a new instance of the query builder for the entities connection, call the following;

    $this->query();
    
You will need to tell Articulate to hydrate your entities, but that can be done by doing one of the following;

    $this->hydrate($results)
    
The above is available within a repository that extends the DatabaseRepository.

    $this->manager()->hydrate(EntityClass, $row);

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

### Migrations

Migrations are exactly the same, this package doesn't add anything specific regarding that.

