<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

class HasOne extends BelongsTo
{
    public function __construct(string $foreignKey, string $localKey = 'id')
    {
        parent::__construct($localKey, $foreignKey);
    }
}