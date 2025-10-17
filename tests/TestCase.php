<?php

namespace SteadfastCollective\LaravelSystemLog\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Factory::guessFactoryNamesUsing(
        //     fn (string $modelName) => 'Patabugen\\MssqlChanges\\Tests\\Fixtures\Database\\Factories\\'.class_basename($modelName)
        //         .'Factory'
        // );
    }

    protected function defineDatabaseMigrations()
    {
        $this->artisan('migrate:fresh')->run();
        $this->loadMigrationsFrom(base_path('database/migrations'));
    }

    // protected function getPackageProviders($app)
    // {
    //     return [
    //         MssqlChangesServiceProvider::class,
    //         \Spatie\LaravelRay\RayServiceProvider::class,
    //     ];
    // }

    public function defineEnvironment($app)
    {
        $this->defineDatabaseMigrations();
    }

    //     config()->set('database.connections.default', [
    //         'driver' => env('DB_DRIVER', 'sqlsrv'),
    //         'host' => env('DB_HOST', 'sqlsrv'),
    //         'port' => env('DB_PORT', '1433'),
    //         'database' => env('DB_DATABASE', 'LaravelMssqlChangesTest'),
    //         'username' => env('DB_USERNAME', 'sa'),
    //         'password' => env('DB_PASSWORD', 'password'),
    //         'url' => '',
    //         'charset' => 'utf8mb4',
    //         'collation' => 'utf8mb4_unicode_ci',
    //         'prefix' => '',
    //         'prefix_indexes' => true,
    //         'strict' => true,
    //         'engine' => null,
    //         'trust_server_certificate' => true,
    //         'options' => [
    //             'TrustServerCertificate' => '1',
    //         ],
    //     ]);
    // }
}
