<?php

namespace ASharifnezhad\ApiDoc\Commands;

use ASharifnezhad\ApiDoc\Facades\DocGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
        $data = DocGenerator::routeFilter(Route::getRoutes())
            ->generate()
            ->getData();

        $this->savePaths($data);
    }

    private function savePaths($data)
    {
        Storage::disk('public')->put('apidoc.json', json_encode($data));
    }
}
