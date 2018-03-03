<?php

namespace Ollieread\Articulate\Columns;

class IntColumn extends BaseColumn
{
    public function cast(string $value): int
    {
        return (int) $value;
    }

    public function toDatabase($value): string
    {
        return (int) $value;
    }
}