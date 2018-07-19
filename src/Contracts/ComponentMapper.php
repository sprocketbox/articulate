<?php

namespace Sprocketbox\Articulate\Contracts;

use Sprocketbox\Articulate\Components\ComponentMapping;

interface ComponentMapper
{
    public function component(): string;

    public function map(ComponentMapping $mapping);
}