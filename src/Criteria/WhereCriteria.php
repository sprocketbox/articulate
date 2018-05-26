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

    public function __construct(string $column, string $operator, $value, int $priority = 0, array $validEntities = [])
    {
        parent::__construct($priority, $validEntities);
        $this->column   = $column;
        $this->operator = $operator;
        $this->value    = $value;
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