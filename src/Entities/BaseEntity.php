<?php

namespace Ollieread\Articulate\Entities;

use Ollieread\Articulate\Concerns;
use Ollieread\Articulate\Contracts\Entity;

abstract class BaseEntity implements Entity
{
    use Concerns\HasAttributes;

    protected $persisted = false;

    public static function hydrated($entity): void
    {
    }

    public function isPersisted(): bool
    {
        return $this->persisted;
    }

    public function setPersisted()
    {
        $this->persisted = true;

        return $this;
    }
}