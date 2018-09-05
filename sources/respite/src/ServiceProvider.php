<?php

namespace Sprocketbox\Articulate\Sources\Respite;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        entities()->registerSource(new RespiteSource);
    }
}