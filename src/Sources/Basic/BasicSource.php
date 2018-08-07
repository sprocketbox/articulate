<?php

namespace Sprocketbox\Articulate\Sources\Basic;

use Sprocketbox\Articulate\Contracts\Source;

class BasicSource implements Source
{

    /**
     * @param string $entity
     * @param string $source
     *
     * @return \Sprocketbox\Articulate\Contracts\EntityMapping
     */
    public function newMapping(string $entity)
    {
        return new BasicEntityMapping($entity, $this->name());
    }

    public function builder(...$arguments)
    {
        return null;
    }

    public function name(): string
    {
        return 'builder';
    }
}