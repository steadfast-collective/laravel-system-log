<?php

namespace SteadfastCollective\LaravelSystemLog;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SteadfastCollective\LaravelSystemLog\Commands\MakeFilamentSystemLogResourceCommand;

class LaravelSystemLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('system-log')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommands()
            ->discoversMigrations()
            ->hasCommand(MakeFilamentSystemLogResourceCommand::class);
    }
}
