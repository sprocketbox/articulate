<?php

namespace Sprocketbox\Articulate\Sources\Illuminate;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Sprocketbox\Articulate\Sources\Illuminate\Grammar\MySQLGrammar;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerRecursive();
        $this->registerAuth();

        entities()->registerSource(new IlluminateSource);
    }

    private function registerRecursive(): void
    {
        Builder::macro('recursive', function (string $name, \Closure $closure) {
            // We only want to add recursive support for MySQL
            if ($this->grammar instanceof \Illuminate\Database\Query\Grammars\MySqlGrammar) {
                $this->bindings['recursive'] = [];
                $recursive                   = $this->newQuery();
                $closure($recursive);
                $this->recursives[] = [$name, $recursive];
                $this->addBinding($recursive->getBindings(), 'recursive');
                $this->grammar = new MysqlGrammar();
            }

            return $this;
        });
    }

    private function registerAuth(): void
    {
        Auth::provider('articulate', function ($app, array $config) {
            return new ArticulateUserProvider($app['hash'], $this->entities->repository($config['entity']), $config['entity']);
        });
    }
}