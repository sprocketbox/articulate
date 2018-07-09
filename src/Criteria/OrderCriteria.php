<?php

namespace Ollieread\Articulate\Criteria;

class OrderCriteria extends BaseCriteria
{
    /**
     * @var array
     */
    protected $columns;

    /**
     * @var string
     */
    protected $direction;

    public function __construct($columns, string $direction = 'desc')
    {
        $this->columns   = (array) $columns;
        $this->direction = $direction;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return mixed
     */
    public function perform($query)
    {
        collect($this->columns)->each(function (string $column) use ($query) {
            $query->orderBy($column, $this->direction);
        });
    }
}