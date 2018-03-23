<?php

namespace Ollieread\Articulate\Columns;

class JsonColumn extends BaseColumn
{
    public function getDefault()
    {
        return parent::getDefault() ?? [];
    }

    public function cast($value)
    {
        return $value ? json_decode($value, true) : $this->getDefault();
    }

    public function toDatabase($value): string
    {
        return json_encode($value);
    }
}