<?php

namespace Ollieread\Articulate\Entities;

use Ollieread\Articulate\Concerns;
use Ollieread\Articulate\Contracts\Entity;

abstract class BaseEntity implements Entity
{
    use Concerns\HasAttributes;
}