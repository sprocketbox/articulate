<?php

namespace Sprocketbox\Articulate\Concerns;

use Illuminate\Support\Collection as LaravelCollection;
use Sprocketbox\Articulate\Contracts\Criteria;

trait HandlesCriteria
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * @return LaravelCollection
     */
    public function getCriteria(): LaravelCollection
    {
        return $this->criteria ?? ($this->criteria = new LaravelCollection);
    }

    /**
     * @param \Sprocketbox\Articulate\Contracts\Criteria|string $criteria
     *
     * @return $this
     */
    public function pushCriteria(Criteria ...$criteria): self
    {
        foreach ($criteria as $criterion) {
            if ($criterion->validFor($this->entity())) {
                $this->getCriteria()->push($criterion);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function withCriteria(): self
    {
        $this->skipCriteria = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function withoutCriteria(): self
    {
        $this->skipCriteria = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetCriteria(): self
    {
        $this->criteria = new LaravelCollection;
        return $this;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    protected function applyCriteria($query)
    {
        if (! $this->skipCriteria) {
            $this->getCriteria()->each(function (Criteria $criteria) use ($query) {
                $criteria->perform($query);
            });
        }

        return $query;
    }
}