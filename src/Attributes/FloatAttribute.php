<?php

namespace Sprocketbox\Articulate\Attributes;

class FloatAttribute extends BaseAttribute
{
    public function cast($value, array $data = [])
    {
        return (float) $value;
    }

    public function parse($value, array $data = [])
    {
        return $value;
    }
}