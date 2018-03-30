<?php

namespace Ollieread\Articulate\Columns;

class FloatColumn extends BaseColumn
{
    public function cast($value)
    {
        return (float) $value;
    }

    public function toDatabase($value)
    {
        return $value;
    }
}