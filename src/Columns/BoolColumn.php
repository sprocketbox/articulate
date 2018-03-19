<?php

namespace Ollieread\Articulate\Columns;

class BoolColumn extends BaseColumn
{

    /**
     * @param $value
     *
     * @return bool
     */
    public function cast($value): bool
    {
        return (bool) $value;
    }

    public function toDatabase($value): int
    {
        return (int) $value;
    }
}