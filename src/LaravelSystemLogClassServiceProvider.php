<?php

namespace SteadfastCollective\LaravelSystemLog;

use Illuminate\Support\Facades\Route;
use Spatie\Example\Commands\ExampleCommand;
use Spatie\Example\Http\Controllers\MyController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSystemLogClassServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-system-log')
            ->hasConfigFile()
            ->hasMigration('create_my_models_table');
    }

    public function packageRegistered()
    {

    }
}
