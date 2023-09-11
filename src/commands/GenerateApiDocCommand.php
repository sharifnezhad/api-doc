<?php

namespace ASharifnezhad\ApiDoc\commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Mpociot\Reflection\DocBlock;
use ReflectionClass;

class GenerateApiDocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apidoc:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Api doc';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $data = collect($routes)->mapWithKeys(function (\Illuminate\Routing\Route $route){
            $uri = $route->uri();
            $methods = $route->methods();
            $controller = $route->getAction('controller');
            $controller = explode('@', $controller);

            if (!$controller[0]){
                return [];
            }
            $phpDocClass = $this->getPhpDocClass($controller[0]);

            $phpDocMethod = $this->getPhpDocMethod($controller[0], $controller[1] ?? '__invoke');
            if (empty($phpDocMethod->getTags()) || empty($phpDocClass->getTags())) {
                return [];
            }
            return [
                $phpDocClass->getTagsByName('group')[0]->getContent() => [
                    $uri => [
                        $methods
                    ]
                ]
            ];
        });
    }

    /**
     * @param $class
     * @return DocBlock
     */
    function getPhpDocClass(string $class): DocBlock
    {
        $class = new ReflectionClass($class);
        return new DocBlock($class);
    }
    /**
     * @param $class
     * @return DocBlock

     */
    private function getPhpDocMethod(string $class, string $method = '') : DocBlock
    {
        if (empty($method)){
            return '';
        }
        $method = new \ReflectionMethod($class, $method);
        return new DocBlock($method);
    }
}
