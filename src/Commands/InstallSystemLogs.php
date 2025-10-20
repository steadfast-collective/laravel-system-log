<?php

namespace SteadfastCollective\LaravelSystemLog\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallSystemLogs extends Command
{
    public $signature = 'system-log:install {--panel=Admin}';

    public $description = 'Install System Logs';

    protected $files;

    protected string $filamentNamespace = '';

    protected string $namespace = 'App';

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        // Create the local SystemLog model
        $panel = 'pdmin';

        $this->publishStub(
            'Models/SystemLog.php',
            app_path('Models/'),
        );

        $this->publishStub(
            'database/factories/SystemLogFactory.php',
            database_path('factories/'),
        );

        if (! class_exists("Filament\Commands\MakeResourceCommand")) {
            $this->info('Not creating Filament Resources because they do not exist');
        }

        $panel = Str::of($this->option('panel'))->title()->toString();
        $this->filamentNamespace = "App\Filament\\{$panel}";
        $path = app_path("Filament/{$panel}/Resources/SystemLogs");

        $this->makeDirectory($path.'/Resources');
        $this->makeDirectory($path.'/Resources/SystemLogs');
        $this->makeDirectory($path.'/Resources/SystemLogs/Pages');
        $this->makeDirectory($path.'/Resources/SystemLogs/Schemas');
        $this->makeDirectory($path.'/Resources/SystemLogs/Tables');

        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/SystemLogResource.php',
            "{$path}/",
        );
        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/Pages/EditSystemLog.php',
            "{$path}/Pages/",
        );
        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/Pages/ListSystemLogs.php',
            "{$path}/Pages/",
        );
        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/Pages/ViewSystemLog.php',
            "{$path}/Pages/",
        );
        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/Schemas/SystemLogForm.php',
            "{$path}/Schemas/",
        );
        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/Schemas/SystemLogInfolist.php',
            "{$path}/Schemas/",
        );
        $this->publishStub(
            'Filament/PanelName/Resources/SystemLogs/Tables/SystemLogsTable.php',
            "{$path}/Tables/",
        );

        return self::SUCCESS;
    }

    private function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }
    }

    private function publishStub($source, $destination)
    {
        $filename = basename($source);
        $destination .= $filename;

        $stub = $this->files->get(__DIR__.'/../stubs/'.$source);

        // Replace Filament-specific namespaces first (so they can include the Panel Name)
        $stub = str_replace('SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName', $this->filamentNamespace, $stub);

        // Replace generic namespaces
        $stub = str_replace('SteadfastCollective\LaravelSystemLog\Stubs', $this->namespace, $stub);

        $this->files->put($destination, $stub);

        $this->info("Published {$destination}");
    }
}
