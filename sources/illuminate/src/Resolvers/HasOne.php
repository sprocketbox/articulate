<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Resolvers;

class HasOne extends BelongsTo
{
    public function __construct(string $entity, string $relatedEntity, string $relatedKey, string $localKey = 'id')
    {
        parent::__construct($entity, $relatedEntity, $localKey, $relatedKey);
    }

    public function getLocalKey(): ?string
    {
        return null;
    }
}