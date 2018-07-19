<?php

namespace Sprocketbox\Articulate\Criteria;

class ByKey extends BaseCriteria
{
    /**
     * @var mixed
     */
    protected $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param \Sprocketbox\Articulate\Sources\Illuminate\IlluminateBuilder $query
     *
     * @return mixed
     */
    public function perform($query)
    {
        $column = entities()->getMapping($query->getEntity())->getKey();
        $query->where($column, '=', $this->key);
    }
}