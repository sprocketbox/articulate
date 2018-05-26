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

    public function getAllCriteria(): Collection
    {
        return $this->criteria ?? ($this->criteria = new Collection);
    }

    /**
     * @param string $criteriaClass
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCriteria(string $criteriaClass): Collection
    {
        return $this->getAllCriteria()->filter(function (Criteria $criteria) use ($criteriaClass) {
            return \get_class($criteria) === $criteriaClass;
        });
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Criteria|string $criteria
     *
     * @return $this
     */
    public function withCriteria($criteria): self
    {
        if (! ($criteria instanceof Criteria)) {
            if (class_exists($criteria)) {
                $criteria = new $criteria;
            } else {
                throw new \InvalidArgumentException('Invalid criteria');
            }
        }

        $this->getAllCriteria()->push($criteria);

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

    public function hasCriteria(string $criteriaClass): bool
    {
        return $this->getAllCriteria()->contains(function (Criteria $criteria) use ($criteriaClass) {
            return \get_class($criteria) === $criteriaClass;
        });
    }

    /**
     * @param $query
     */
    protected function performCriteria($query): void
    {
        $criteria = $this->getAllCriteria()->sortBy(function (Criteria $criteria) {
            return $criteria->getPriority();
        });

        $criteria->each(function (Criteria $criteria) use ($query) {
            $criteria->perform($query);
        });
    }
}