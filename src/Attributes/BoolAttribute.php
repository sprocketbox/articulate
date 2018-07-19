<?php

namespace Sprocketbox\Articulate\Attributes;

class BoolAttribute extends BaseAttribute
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

    public function parse($value): int
    {
        return (int) $value;
    }
}