<?php

namespace Ollieread\Articulate;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Ollieread\Articulate\Auth\ArticulateUserProvider;
use Ollieread\Articulate\Contracts\Mapping as MappingContract;

/**
 * Class ServiceProvider
 *
 * @package Ollieread\Articulate
 */
class ServiceProvider extends BaseProvider
{
    /**
     * @var \Ollieread\Articulate\EntityManager
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

        if (config('articulate.auth') === true) {
            $this->registerAuth();
        }
    }

    /**
     *
     */
    private function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/articulate.php' => config_path('articulate.php'),
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

        $this->app->bind(MappingContract::class, function ($app, array $arguments) {
            [$entity, $connection, $table] = $arguments;
            return new Mapping($entity, $connection, $table);
        });

        $this->registerEntities();
    }

    /**
     *
     * @throws \RuntimeException
     */
    private function registerEntities(): void
    {
        $mappings = config('articulate.mappings', []);

        foreach ($mappings as $mapping) {
            $this->entities->register(new $mapping);
        }
    }

    private function registerAuth()
    {
        Auth::provider('articulate', function ($app, array $config) {
            return new ArticulateUserProvider($app['hash'], $this->entities->repository($config['entity']), $config['entity']);
        });
    }
}