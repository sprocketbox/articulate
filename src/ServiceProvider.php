<?php

namespace Ollieread\Articulate;

use Illuminate\Support\ServiceProvider as BaseProvider;

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
}