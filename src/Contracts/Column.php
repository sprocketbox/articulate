<?php

namespace Ollieread\Articulate\Contracts;

interface Column
{
    public function cast(string $value);

    public function getColumnName(): string;

    public function setImmutable();

    public function isImmutable(): bool;

    public function setDynamic();

    public function isDynamic(): bool;

    public function toDatabase($value): string;
}