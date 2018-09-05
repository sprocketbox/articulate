<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Sprocketbox\Articulate\Entities\EntityMapping;

class RespiteEntityMapping extends EntityMapping
{
    /**
     * @var string
     */
    protected $provider;

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return \Sprocketbox\Articulate\Sources\Respite\RespiteEntityMapping
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
}