<?php

namespace Ollieread\Articulate\Criteria;

class WhereCriteria extends BaseCriteria
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct(string $column, string $operator, $value = null)
    {;
        $this->column   = $column;
        $this->operator = $value ? $operator : '=';
        $this->value    = $value ?? $operator;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return mixed
     */
    public function perform($query)
    {
        if (\is_array($this->value)) {
            $query->whereIn($this->column, $this->value);
        } else {
            $query->where($this->column, $this->operator, $this->value);
        }
    }
}