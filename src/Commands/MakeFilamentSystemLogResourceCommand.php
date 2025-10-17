<?php

namespace SteadfastCollective\LaravelSystemLog\Commands;

use Illuminate\Console\Command;

class MakeFilamentSystemLogResourceCommand extends Command
{
    public $signature = 'system-log:make-filament-resource';

    public $description = 'Generate a Filament Resource for managing your System Logs';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
