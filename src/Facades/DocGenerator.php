<?php

namespace ASharifnezhad\ApiDoc\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static routeFilter($routes)
 * @see \ASharifnezhad\ApiDoc\Classes\DocGenerator
 */
class DocGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'DocGenerator';
    }
}
