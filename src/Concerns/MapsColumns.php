<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Contracts\Column;

trait MapsColumns
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Column $type
     *
     * @return \Ollieread\Articulate\Contracts\Column
     */
    public function mapColumn(Column $type): Column
    {
        $this->columns->put($type->getColumnName(), $type);
        return $type;
    }

    public function getColumn(string $column): ?Column
    {
        return $this->columns->get($column, null);
    }
}