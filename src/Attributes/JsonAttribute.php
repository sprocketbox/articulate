<?php

namespace Sprocketbox\Articulate\Attributes;

class JsonAttribute extends BaseAttribute
{
    public function getDefault()
    {
        return parent::getDefault() ?? [];
    }

    public function cast($value, array $data = [])
    {
        return $value ? json_decode($value, true) : $this->getDefault();
    }

    public function parse($value, array $data = []): string
    {
        return json_encode($value);
    }
}