<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
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
     * @return \Illuminate\Support\Collection
     */
    public function getCriteria(): Collection
    {
        return $this->criteria ?? ($this->criteria = new Collection);
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
        $this->criteria = new Collection;
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
            $criteria = $this->getCriteria()->sortBy(function (Criteria $criteria) {
                return $criteria->getPriority();
            });

            $criteria->each(function (Criteria $criteria) use ($query) {
                $criteria->perform($query);
            });
        }

        return $query;
    }
}