<?php

namespace Sprocketbox\Articulate\Attributes;

class ArrayAttribute extends BaseAttribute
{

    public function cast($value, array $data = [])
    {
        return (array)$value;
    }

    public function parse($value, array $data = [])
    {
        return (array)$value;
    }
}