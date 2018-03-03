<?php

namespace Ollieread\Articulate\Columns;

class StringColumn extends BaseColumn
{
    public function cast(string $value): string
    {
        return (string) $value;
    }

    public function toDatabase($value): string
    {
        return (string) $value;
    }
}