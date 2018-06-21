<?php

namespace Ollieread\Articulate\Concerns;

use Carbon\Carbon;

trait HasTimestamps
{
    public function getCreatedAt(): ?Carbon
    {
        return $this->getAttribute('created_at');
    }

    public function setCreatedAt(Carbon $value): self
    {
        $this->setAttribute('created_at', $value);

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->getAttribute('created_at');
    }

    public function setUpdatedAt(Carbon $value): self
    {
        $this->setAttribute('updated_at', $value);

        return $this;
    }
}