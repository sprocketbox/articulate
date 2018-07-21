<?php

namespace Sprocketbox\Articulate\Attributes;

class BoolAttribute extends BaseAttribute
{

    /**
     * @param       $value
     * @param array $data
     *
     * @return bool
     */
    public function cast($value, array $data = []): bool
    {
        return (bool) $value;
    }

    public function parse($value, array $data = []): int
    {
        return (int) $value;
    }
}