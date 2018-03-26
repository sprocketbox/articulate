<?php

namespace Ollieread\Articulate\Contracts;

interface EntityMapping
{
    public function entity(): string;

    public function connection(): string;

    public function table(): ?string;

    public function map(Mapping $mapper);
}