<?php

namespace SteadfastCollective\LaravelSystemLog\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use SteadfastCollective\LaravelSystemLog\LaravelSystemLogServiceProvider;
use SteadfastCollective\LaravelSystemLog\Models\SystemLog;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set the config to use our internal SystemLog class since there isn't an
        // extended app-specific one
        config([
            'system-log' => [
                'class' => SystemLog::class,
            ],
        ]);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'SteadfastCollective\\LaravelSystemLog\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelSystemLogServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__.'/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
