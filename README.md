# Articulate #

[![Latest Stable Version](https://poser.pugx.org/ollieread/articulate/v/stable.png)](https://packagist.org/packages/ollieread/articulate) [![Total Downloads](https://poser.pugx.org/ollieread/articulate/downloads.png)](https://packagist.org/packages/ollieread/articulate) [![Latest Unstable Version](https://poser.pugx.org/ollieread/articulate/v/unstable.png)](https://packagist.org/packages/ollieread/articulate) [![License](https://poser.pugx.org/ollieread/articulate/license.png)](https://packagist.org/packages/ollieread/articulate)

- **Laravel**: 5.5, 5.6
- **PHP**: 7.1+
- **Author**: Ollie Read 
- **Author Homepage**: http://ollieread.com

Articulate is a drop in replacement for Eloquent. It moves away from ActiveRecord pattern and finds a nice area that sits neatly between basic queries and the EntityMapper pattern.

Doctrine is a wonderful package, but it's a monolith. This package is designed to bring a level of optimisation and simplicity, with some of the nice features of both worlds.

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

    Ollieread\Articulate\Facades\Entities
    
Or you can inject the following manager;

    Ollieread\Articulate\Entities

### Entities

Entities are basic value key => value stores and represent a row in your database.

An entity should have a getter and a setter for the columns in the database. 

The method name should match the column name is studly case, with getters being prefixed with `get` and setters with `set`. For example;

    class Test
    {
        protected $id;
        protected $name;
        
        public function getId(): int
        {
            return $this->id;
        }
        
        public function setId(string $id): Test
        {
            $this->id = $id;
    
            return $this;
        }
        
        public function getName(): string
        {
            return $this->name;
        }
        
        public function setName(string $name): Test
        {
            $this->name = $name;
    
            return $this;
        }
    }

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

An example implementation would be as follows;

    public function map(Mapper $mapper)
    {
        $mapper->setKey('id');
        $mapper->setRepository(TestRepository::class);
    }

### Repositories

Repositories are how we retrieve instance of our entities. If you're familiar with the repository pattern, you'll recognise this particular way of doing things.

All user defined repositories should extend the following class;

    Ollieread\Articulate\Repositories\EntityRepository
    
Within here you may define your own methods as you see fit.

To access an entity specific query builder you will need to call `$this->query()`. The query builder returned is the same as the Laravel query builder, even though it appears as the following class;

    Ollieread\Articular\Database\Builder
    
An example repository would be as follows;

    class TestRepository extends EntityRepository
    {
        public function all()
        {
            return $this->query()->get();
        }
    }
    
The `all()` method here would return a `Collection` of `Test` entities.



