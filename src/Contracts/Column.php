<?php

namespace Ollieread\Articulate\Contracts;

interface Column
{
    public function cast($value);

    public function getColumnName(): string;

    public function setColumnName(string $name);

    public function getAttributeName(): string;

    public function setImmutable();

    public function isImmutable(): bool;

    public function setDynamic();

    public function isDynamic(): bool;

    public function toDatabase($value);

    public function getDefault();

    public function setDefault($default);
}