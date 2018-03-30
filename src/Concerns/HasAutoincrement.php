<?php

namespace Ollieread\Articulate\Concerns;

trait HasAutoincrement
{

    public function getId(): int
    {
        return $this->get('id');
    }

    public function setId(int $value): self
    {
        $this->set('id', $value);

        return $this;
    }
}