<?php

namespace SteadfastCollective\LaravelSystemLog\Commands;

use Illuminate\Console\Command;

class LaravelSystemLogCommand extends Command
{
    public $signature = 'laravel-system-log';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
