<?php

namespace Ollieread\Articulate\Concerns;

use Carbon\Carbon;

trait HasTimestamps
{
    public function getCreatedAt(): ?Carbon
    {
        return $this->get('created_at');
    }

    public function setCreatedAt(Carbon $value): self
    {
        $this->set('created_at', $value);

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->get('created_at');
    }

    public function setUpdatedAt(Carbon $value): self
    {
        $this->set('updated_at', $value);

        return $this;
    }
}