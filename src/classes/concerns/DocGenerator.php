<?php

namespace ASharifnezhad\ApiDoc\classes\concerns;

use ASharifnezhad\ApiDoc\classes\concerns\Methods\GetMethod;
use ASharifnezhad\ApiDoc\classes\concerns\Methods\PostMethod;
use Illuminate\Routing\Route as RouteClass;
use Mpociot\Reflection\DocBlock;
use ReflectionClass;
use ReflectionMethod;

class DocGenerator
{
    protected $config;
    protected $routes;
    public array $paths = [];
    private string $methodInsideController;
    private string $controller;
    private array $headers;

    public function __construct($config)
    {
        $this->config = $config;
        $this->setDefaultValues();
        $this->setHeader();
    }

    public function routeFilter($routes): static
    {

        $routeConfig = collect($this->config['routes']['prefixes'])->map(fn($route) => trim($route, '/'))->toArray();

        if (count($routeConfig) <= 1 && $routeConfig[0] = '*') {
            $this->routes = $routes;
            return $this;
        }

        $this->routes = collect($routes)->filter(function (RouteClass $route) use ($routeConfig) {
            return collect($routeConfig)->first(fn($config) => str_contains($route->uri(), $config));
        });
        return $this;
    }

    public function generate(): static
    {
        $pathData = [];
        $data['paths'] = collect($this->routes)
            ->mapWithKeys(function (RouteClass $route) use (&$pathData) {
                $uri = $route->uri();
                $methods = $route->methods();
                $this->setControllerAndMethod($route);

                if (!isset($this->controller)) {
                    return [];
                }

                $phpDocClass = $this->getPhpDocClass($this->controller);
                $phpDocMethod = $this->getPhpDocMethod($this->controller, $this->methodInsideController);

                if (empty($phpDocMethod->getTags()) || empty($phpDocClass->getTags())) {
                    return [];
                }

                $customMethod = app($this->getMethodClass($methods[0]));
                $pathData = array_merge($pathData, $customMethod->methodParams([
                    'uri' => $uri,
                    'methods' => $methods,
                    'headers' => $this->headers,
                    'bodyParameters' => $phpDocMethod->getTagsByName('bodyParam'),
                    'queryParameters' => $phpDocMethod->getTagsByName('pathParam')
                ], $phpDocClass, $phpDocMethod));

                return [$uri => $pathData];

            })->toArray();

        $this->paths = array_merge($this->paths, $data);

        return $this;
    }

    private function setControllerAndMethod(RouteClass $route)
    {
        $controller = $route->getAction('controller');
        $controller = explode('@', $controller);

        if (!$controller[0]) {
            return false;
        }

        $this->controller = $controller[0];
        $this->methodInsideController = $controller[1] ?? '__invoke';

        return true;
    }

    private function setDefaultValues(): static
    {
        $this->paths = [
            "openapi" => "3.0.0",
            "info" => [
                "title" => $this->config['title'],
                "version" => $this->config['version'],
                "description" => $this->config['description'],
                "license" => $this->config['license'],
                "x-logo" => [
                    "url" => $this->config['logo'],
                    "altText" => $this->config['title'],
                    "backgroundColor" => $this->config['color']
                ]
            ],
            'servers' => $this->config['servers'],
            'components' => [
                'securitySchemes' => $this->config['security']
            ]
        ];

        return $this;
    }

    private function getPhpDocClass(string $class): DocBlock
    {
        $class = new ReflectionClass($class);
        return new DocBlock($class);
    }

    private function getPhpDocMethod(string $class, string $method): DocBlock
    {
        $method = new ReflectionMethod($class, $method);
        return new DocBlock($method);
    }

    private function getMethodClass(string $method)
    {
        return $this->config['methods'][$method];
    }

    private function setHeader()
    {
        $this->headers = $this->config['routes']['headers'];

        return $this;
    }

    public function getData()
    {
        return $this->paths;
    }
}
