<?php

namespace Sprocketbox\Articulate;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Sprocketbox\Articulate\Components\ComponentMapping;
use Sprocketbox\Articulate\Contracts\ComponentMapping as ComponentMappingContract;
use Sprocketbox\Articulate\Contracts\EntityMapping as EntityMappingContract;
use Sprocketbox\Articulate\Entities\EntityMapping;

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

        $this->registerSources();
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
    }

    /**
     *
     * @throws \RuntimeException
     */
    private function registerSources(): void
    {
        $sources = config('articulate.sources', []);

        foreach ($sources as $source) {
            if (class_exists($source)) {
                $this->entities->registerSource($source);
            }
        }
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
}