<?php

return [

    'mappings' => [

    ],

    'columns' => [
        'bool'        => \Ollieread\Articulate\Columns\BoolColumn::class,
        'entity'      => \Ollieread\Articulate\Columns\EntityColumn::class,
        'int'         => \Ollieread\Articulate\Columns\IntColumn::class,
        'json'        => \Ollieread\Articulate\Columns\JsonColumn::class,
        'string'      => \Ollieread\Articulate\Columns\StringColumn::class,
        'timestamp'   => \Ollieread\Articulate\Columns\TimestampColumn::class,
        'float'       => \Ollieread\Articulate\Columns\FloatColumn::class,
        //'object_id'   => \Ollieread\Articulate\Columns\MongoDB\ObjectIdColumn::class,
        //'subdocument' => \Ollieread\Articulate\Columns\MongoDB\SubdocumentColumn::class,
        //'utc'         => \Ollieread\Articulate\Columns\MongoDB\UtcColumn::class,
    ],

    'auth' => true,

];