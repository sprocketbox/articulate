# Installation
Articulate is available as a composer package on [Packagist](https://packagist.org/packages/ollieread/articulate), you can install it using Composer.

To install, simply require the package using composer.

    composer require sprocketbox/articulate

Once the package is installed, publish the configuration using the following command.

    php artisan vendor:publish --provider=Sprocketbox\Articulate\ServiceProvider
    
# Configuration
The Articulate configuration file will be created at `LARAVEL_BASE_DIR/config/articulate.php`.

## Mappers
To register your mappers with Articulate, you just need to add a class reference to the `articulate.mapper` configuration array.

    'mapper' => [
        \App\Mappings\TestMapper::class,
    ],

You can read more about mappers [here](/breakdown/mappers).

## Attributes
To change the default attribute availability, or add your own custom attributes, you need to add to the `articulate.attributes` 
configuration array, in the format of `snake_case_ident => Attribute::class`. The entity mapping will convert this snake 
case ident to camel case for usage in your mappings.

    'attributes' => [
        'bool'      => \Sprocketbox\Articulate\Attributes\BoolAttribute::class,
        'entity'    => \Sprocketbox\Articulate\Attributes\EntityAttribute::class,
        'int'       => \Sprocketbox\Articulate\Attributes\IntAttribute::class,
        'json'      => \Sprocketbox\Articulate\Attributes\JsonAttribute::class,
        'string'    => \Sprocketbox\Articulate\Attributes\StringAttribute::class,
        'timestamp' => \Sprocketbox\Articulate\Attributes\TimestampAttribute::class,
        'float'     => \Sprocketbox\Articulate\Attributes\FloatAttribute::class,
        'text'      => \Sprocketbox\Articulate\Attributes\TextAttribute::class,
        //'object_id'   => \Sprocketbox\Articulate\Attributes\MongoDB\ObjectIdColumn::class,
        //'subdocument' => \Sprocketbox\Articulate\Attributes\MongoDB\SubdocumentColumn::class,
        //'utc'         => \Sprocketbox\Articulate\Attributes\MongoDB\UtcColumn::class,
    ],

There are 3 available MongoDB attributes that are commented out by default. To use them, simply uncomment.

You can read more about attributes [here](/breakdown/attributes).

## Sources
To register sources with articulate, you can modify the `articulate.sources` config option. All sources are listed in the
format of `ident => sourceClass`. There are two sources present by default, though one is commented out as it relies
on another package.

    'sources' => [
        'illuminate' => \Sprocketbox\Articulate\Sources\Illuminate\IlluminateSource::class,
        //'respite'    => \Sprocketbox\Articulate\Sources\Respite\RespiteSource::class,
    ], 
    
You can read more about sources [here](/breakdown/sources).

## Extras
Articulate ships with two additional pieces of functionality that you can enable or disable as you see fit.

    'extra' => [
        'auth'      => true,
        'recursive' => false,
    ],

### Laravel Auth
Setting the `articulate.extra.auth` option to true will register the authentication driver for Articulate, allowing you
to use entities with the default auth library.
    
You can read more about auth [here](/extras/#auth).

### Illuminate Recursive Queries
Setting the `articulate.extra.recursive` option to true will register a query builder mixin that allows you to perform recursive CTE queries.
Currently this only works with MySQL.
    
You can read more about recursive queries [here](/extras/#recursive).