<?php

namespace Sprocketbox\Articulate\Sources\Illuminate\Criteria;

use Sprocketbox\Articulate\Criteria\BaseCriteria;

class ByKey extends BaseCriteria
{
    protected $key;

    public function __construct($key)
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
        $mapping = entities()->mapping($query->getEntity());

        if ($mapping) {
            $keyName = $mapping->getKey();
            $query->where($keyName, '=', $this->key);
        }
    }
}