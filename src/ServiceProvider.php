<?php

namespace Ollieread\Articulate;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    /**
     * @var \Ollieread\Articulate\EntityManager
     */
    protected $entities;

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
        }
    }

    private function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/articulate.php' => config_path('articulate.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->entities = new EntityManager;
        $this->app->instance(EntityManager::class, $this->entities);
        $this->app->instance('entities', $this->entities);

        $this->registerEntities();
    }

    private function registerEntities(): void
    {
        $mappings = config('articulate.mappings', []);

        foreach ($mappings as $mapping) {
            $this->entities->register(new $mapping);
        }
    }
}