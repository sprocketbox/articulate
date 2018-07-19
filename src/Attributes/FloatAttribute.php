<?php

namespace Sprocketbox\Articulate\Attributes;

class FloatAttribute extends BaseAttribute
{
    public function cast($value)
    {
        return (float) $value;
    }

    public function parse($value)
    {
        return $value;
    }
}