<?php

namespace Sprocketbox\Articulate;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Sprocketbox\Articulate\Auth\ArticulateUserProvider;
use Sprocketbox\Articulate\Components\ComponentMapping;
use Sprocketbox\Articulate\Contracts\EntityMapping as EntityMappingContract;
use Sprocketbox\Articulate\Contracts\ComponentMapping as ComponentMappingContract;
use Sprocketbox\Articulate\Entities\EntityMapping;
use Sprocketbox\Articulate\Sources\Illuminate\Grammar\MySQLGrammar;
use Sprocketbox\Articulate\Sources\Illuminate\IlluminateSource;

/**
 * Class ServiceProvider
 *
 * @package Sprocketbox\Articulate
 */
class ServiceProvider extends BaseProvider
{
    /**
     * @var \Sprocketbox\Articulate\EntityManager
     */
    protected $entities;

    /**
     *
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
        }

        if ((bool)config('articulate.extra.auth') === true) {
            $this->registerAuth();
        }

        if ((bool)config('articulate.extra.recursive') === true) {
            $this->registerRecursive();
        }

        $this->registerEntities();
    }

    /**
     *
     */
    private function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/articulate.php' => config_path('articulate.php'),
        ], 'config');
    }

    /**
     *
     * @throws \RuntimeException
     */
    public function register(): void
    {
        $this->entities = new EntityManager;
        $this->app->instance(EntityManager::class, $this->entities);
        $this->app->instance('entities', $this->entities);

        $this->app->bind(EntityMappingContract::class, function ($app, array $arguments) {
            [$entity, $source] = $arguments;
            return new EntityMapping($entity, $source);
        });

        $this->app->bind(ComponentMappingContract::class, function ($app, array $arguments) {
            [$name] = $arguments;
            return new ComponentMapping($name);
        });

        $this->registerSources();
    }

    /**
     *
     * @throws \RuntimeException
     */
    private function registerEntities(): void
    {
        $mappers = config('articulate.mappers', []);

        foreach ($mappers as $mapper) {
            $this->entities->registerEntity(new $mapper);
        }
    }

    private function registerAuth(): void
    {
        Auth::provider('articulate', function ($app, array $config) {
            return new ArticulateUserProvider($app['hash'], $this->entities->repository($config['entity']), $config['entity']);
        });
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

    private function registerSources(): void
    {
        $sources = config('articulate.sources', []);

        foreach ($sources as $ident => $source) {
            $this->entities->registerSource($ident, $source);
        }
    }
}