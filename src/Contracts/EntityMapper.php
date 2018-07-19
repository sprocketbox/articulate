<?php

namespace Sprocketbox\Articulate\Contracts;

interface EntityMapper
{
    public function entity(): string;

    public function source(): string;

    public function map(EntityMapping $mapping);
}