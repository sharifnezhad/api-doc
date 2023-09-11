<?php

namespace ASharifnezhad\ApiDoc;

use ASharifnezhad\ApiDoc\classes\concerns\DocGenerator;
use ASharifnezhad\ApiDoc\commands\GenerateApiDocCommand;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ApiDocServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'apidoc');
        $this->mergeConfigFrom(__DIR__ . '/config/apidoc.php', 'apidoc');
        $this->commands([
            GenerateApiDocCommand::class
        ]);
        $this->publishes([
            __DIR__ . '/config/apidoc.php' => app()->basePath() . '/config/apidoc.php',
        ], 'apidoc-config');

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
}
