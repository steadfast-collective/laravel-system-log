<?php

namespace SteadfastCollective\LaravelSystemLog\Commands;

use Filament\Support\Commands\Concerns\HasPanel;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallSystemLogs extends Command
{
    use HasPanel;

    public $signature = 'system-log:install {--panel=Admin} {--force}';

    public $description = 'Install System Logs';

    /**
     * Whether to overwrite files if they already exist
     */
    protected bool $force = false;

    /**
     * Filesystem handle for reading/writing files.
     */
    protected Filesystem $files;

    /**
     * The namespace prefix for Resource files, which we use to overwrite the placeholders
     * set in our stub files.
     */
    protected string $resourcesNamespace = '';

    /**
     * The namespace prefix for the app overall - used for setting the Models namespace.
     */
    protected string $namespace = 'App';

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $this->force = $this->option('force');

        $this->configurePanel(question: 'Which panel would you like to create this resource in?');
        $path = $this->panel->getResourceDirectories()[0].'/SystemLogs';
        $this->resourcesNamespace = $this->panel->getResourceNamespaces()[0];

        $this->publishStub(
            'Models/SystemLog.php',
            app_path('Models/'),
        );

        $this->publishStub(
            'database/factories/SystemLogFactory.php',
            database_path('factories/'),
        );

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
            'Filament/PanelName/Resources/SystemLogs/Schemas/SystemLogForm.php',
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

        if ($this->files->exists($destination)) {
            if ($this->force !== true) {
                $this->error("File [$destination] already exists. Skipping");

                return;
            }
        }
        $stub = $this->files->get(__DIR__.'/../stubs/'.$source);

        // Replace Filament-specific namespaces first (so they can include the Panel Name)
        $stub = str_replace('SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources', $this->resourcesNamespace, $stub);

        // Replace generic namespaces
        $stub = str_replace('SteadfastCollective\LaravelSystemLog\Stubs', $this->namespace, $stub);

        $this->files->put($destination, $stub);

        $this->info("Published {$destination}");
    }
}
