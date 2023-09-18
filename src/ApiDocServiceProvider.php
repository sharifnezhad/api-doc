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
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'apidoc');
        $this->mergeConfigFrom(__DIR__ . '/Config/apidoc.php', 'apidoc');
        $this->commands([
            GenerateApiDocCommand::class
        ]);
        $this->publishes([
            __DIR__ . '/Config/apidoc.php' => app()->basePath() . '/config/apidoc.php',
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
