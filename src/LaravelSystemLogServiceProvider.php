<?php

namespace SteadfastCollective\LaravelSystemLog;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SteadfastCollective\LaravelSystemLog\Commands\LaravelSystemLogCommand;

class LaravelSystemLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('system-log')
            ->hasConfigFile()
            ->hasViews()
            ->discoversMigrations()
            ->hasCommand(LaravelSystemLogCommand::class);
    }
}
