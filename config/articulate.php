<?php

return [

    'mappings' => [

    ],

    'columns' => [
        'bool'      => \Ollieread\Articulate\Columns\BoolColumn::class,
        'entity'    => \Ollieread\Articulate\Columns\EntityColumn::class,
        'int'       => \Ollieread\Articulate\Columns\IntColumn::class,
        'json'      => \Ollieread\Articulate\Columns\JsonColumn::class,
        'string'    => \Ollieread\Articulate\Columns\StringColumn::class,
        'timestamp' => \Ollieread\Articulate\Columns\TimestampColumn::class,
    ],

    'auth' => true,

];