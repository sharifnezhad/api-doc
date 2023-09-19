<?php

namespace ASharifnezhad\ApiDoc;

use ASharifnezhad\ApiDoc\Classes\DocGenerator;
use ASharifnezhad\ApiDoc\Commands\GenerateApiDocCommand;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ApiDocServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViews();
        $this->loadConfigs();
        $this->loadCommands();
        $this->loadRoutes();
    }

    public function register()
    {
        $this->app->bind('DocGenerator', function (Application $app) {
            return new DocGenerator(config('apidoc'));
        });

        collect(config('apidoc.methods'))->each(function ($method) {
            $this->app->bind($method, fn(Application $app) => new $method(config('apidoc')));
        });
    }

    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'apidoc');
        $this->publishes([
            __DIR__ . '/Resources/Views/CodeSamples' => $this->app->basePath() . '/resources/views/CodeSamples'
        ], 'apidoc-views');
    }

    private function loadConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/apidoc.php', 'apidoc');
        $this->publishes([
            __DIR__ . '/Config/apidoc.php' => app()->basePath() . '/config/apidoc.php',
        ], 'apidoc-config');
    }

    private function loadCommands(): void
    {
        $this->commands([
            GenerateApiDocCommand::class
        ]);
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }
}
