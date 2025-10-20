<?php

namespace SteadfastCollective\LaravelSystemLog;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SteadfastCollective\LaravelSystemLog\Commands\InstallSystemLogs;

class LaravelSystemLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('system-log')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommands()
            ->hasMigration('create_system_log_table')
            ->hasCommand(InstallSystemLogs::class);
    }
}
