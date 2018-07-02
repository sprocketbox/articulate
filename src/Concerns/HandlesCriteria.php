<?php

namespace Ollieread\Articulate\Concerns;

use Ollieread\Articulate\Support\Collection;
use Illuminate\Support\Collection as LaravelCollection;
use Ollieread\Articulate\Contracts\Criteria;

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
     * @param \Ollieread\Articulate\Contracts\Criteria|string $criteria
     *
     * @return $this
     */
    public function pushCriteria($criteria): self
    {
        if (! ($criteria instanceof Criteria)) {
            if (class_exists($criteria)) {
                try {
                    $criteria = new $criteria;
                } catch (\Exception $e) {
                }
            }
        }

        if ($criteria instanceof Criteria && $criteria->validFor($this->entity())) {
            $this->getCriteria()->push($criteria);
            return $this;
        }

        throw new \InvalidArgumentException('Invalid criteria');
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
     * @return \Ollieread\Articulate\Query\Builder
     */
    protected function applyCriteria($query)
    {
        if (! $this->skipCriteria) {
            $this->getCriteria()->sortBy(function (Criteria $criteria) {
                return $criteria->getPriority();
            })->each(function (Criteria $criteria) use ($query) {
                $criteria->perform($query);
            });
        }

        return $query;
    }
}