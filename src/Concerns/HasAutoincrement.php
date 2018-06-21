<?php

namespace Ollieread\Articulate\Concerns;

trait HasAutoincrement
{

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function setId(int $value): self
    {
        $this->setAttribute('id', $value);

        return $this;
    }
}