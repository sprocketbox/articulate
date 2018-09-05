<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Sprocketbox\Articulate\Contracts\Source;

class RespiteSource implements Source
{

    /**
     * @param string $entity
     * @param string $source
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function newMapping(string $entity)
    {
        return new RespiteEntityMapping($entity, $this->name());
    }

    public function builder(...$arguments)
    {
        /**
         * @var null|string                                                  $entity
         * @var \Sprocketbox\Articulate\Sources\Respite\RespiteEntityMapping $mapping
         */
        [$entity, $mapping] = $arguments;

        return (new RespiteBuilder($this->respite()->for($mapping->getProvider()), entities()))->setEntity($entity ?? $mapping->getEntity());
    }

    public function respite()
    {
        return respite();
    }

    public function name(): string
    {
        return 'respite';
    }
}