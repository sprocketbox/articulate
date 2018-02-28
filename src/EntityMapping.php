<?php

namespace Ollieread\Articulate;

use Ollieread\Articulate\Contracts\EntityMapping as Contract;

abstract class EntityMapping implements Contract
{
    /**
     * This is here so that you don't need to define this method.
     *
     * @return string
     */
    public function connection(): string
    {
        return '';
    }
}