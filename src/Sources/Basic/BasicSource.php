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
    public function newMapping(string $entity, string $source)
    {
        return new BasicEntityMapping($entity, $source);
    }

    public function builder(...$arguments)
    {
        return null;
    }
}