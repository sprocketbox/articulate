<?php

return [

    'mappers' => [

    ],

    'attributes' => [
        'bool'      => \Sprocketbox\Articulate\Attributes\BoolAttribute::class,
        'entity'    => \Sprocketbox\Articulate\Attributes\EntityAttribute::class,
        'int'       => \Sprocketbox\Articulate\Attributes\IntAttribute::class,
        'json'      => \Sprocketbox\Articulate\Attributes\JsonAttribute::class,
        'string'    => \Sprocketbox\Articulate\Attributes\StringAttribute::class,
        'timestamp' => \Sprocketbox\Articulate\Attributes\TimestampAttribute::class,
        'float'     => \Sprocketbox\Articulate\Attributes\FloatAttribute::class,
        'text'      => \Sprocketbox\Articulate\Attributes\TextAttribute::class,
        'array'     => \Sprocketbox\Articulate\Attributes\ArrayAttribute::class,
        'uuid'      => \Sprocketbox\Articulate\Attributes\UuidAttribute::class,
        //'object_id'   => \Sprocketbox\Articulate\Attributes\MongoDB\ObjectIdColumn::class,
        //'subdocument' => \Sprocketbox\Articulate\Attributes\MongoDB\SubdocumentColumn::class,
        //'utc'         => \Sprocketbox\Articulate\Attributes\MongoDB\UtcColumn::class,
    ],

    'extra' => [
        /*
         * Whether or not to enable the articulate authentication driver
         */
        'auth'      => true,

        /*
         * Whether or not to enable the query builder mixin for recursive ctes
         */
        'recursive' => false,
    ],

];