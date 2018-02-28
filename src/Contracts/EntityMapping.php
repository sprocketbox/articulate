<?php

namespace Ollieread\Articulate\Contracts;

use Ollieread\Articulate\Mapper;

interface EntityMapping
{
    public function entity(): string;

    public function connection(): string;

    public function table(): string;

    public function map(Mapper $mapper);
}