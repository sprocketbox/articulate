<?php

namespace Ollieread\Articulate\Columns;

class BoolColumn extends BaseColumn
{

    public function cast($value)
    {
        return (bool) $value;
    }

    public function toDatabase($value): int
    {
        return (int) $value;
    }
}