<?php

namespace Sprocketbox\Articulate\Attributes;

class JsonAttribute extends BaseAttribute
{
    public function getDefault()
    {
        return parent::getDefault() ?? [];
    }

    public function cast($value)
    {
        return $value ? json_decode($value, true) : $this->getDefault();
    }

    public function parse($value): string
    {
        return json_encode($value);
    }
}