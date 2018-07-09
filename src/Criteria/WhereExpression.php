<?php

namespace Ollieread\Articulate\Criteria;

class WhereExpression extends BaseCriteria
{
    /**
     * @var \Illuminate\Database\Query\Expression
     */
    protected $expression;

    public function __construct(string $expression)
    {
        $this->expression   = $expression;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return mixed
     */
    public function perform($query)
    {
        $query->where($this->expression);
    }
}