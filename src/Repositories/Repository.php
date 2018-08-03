<?php

namespace Sprocketbox\Articulate\Repositories;

use Sprocketbox\Articulate\Contracts\Attribute;
use Sprocketbox\Articulate\Contracts\Source;
use Sprocketbox\Articulate\Concerns;
use Sprocketbox\Articulate\Contracts\Repository as Contract;
use Sprocketbox\Articulate\Entities\Entity;
use Sprocketbox\Articulate\EntityManager;
use Sprocketbox\Articulate\Entities\EntityMapping;

/**
 * Class EntityRepository
 *
 * @package Sprocketbox\Articulate\Repositories
 */
abstract class Repository implements Contract
{
    use Concerns\HandlesEntities,
        Concerns\HandlesCriteria;

    /**
     * EntityRepository constructor.
     *
     * @param \Sprocketbox\Articulate\EntityManager          $manager
     * @param \Sprocketbox\Articulate\Entities\EntityMapping $mapping
     * @param \Sprocketbox\Articulate\Contracts\Source       $source
     */
    public function __construct(EntityManager $manager, EntityMapping $mapping, Source $source)
    {
        $this->setManager($manager)->setMapping($mapping)->setSource($source);
    }

    protected function getDirty(Entity $entity)
    {
        return $this->manager()->dehydrate($entity, function ($value, string $name) use ($entity) {
            $attribute = $this->mapping()->getAttribute($name);
            return $attribute && ! $attribute->isImmutable() && ! $attribute->isDynamic() && $entity->isDirty($name);
        });
    }
}