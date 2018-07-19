<?php

namespace Sprocketbox\Articulate\Support;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toBase()
    {
        return new self($this);
    }
}