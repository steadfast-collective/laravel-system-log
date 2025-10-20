<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Pages;

use Filament\Resources\Pages\ListRecords;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\SystemLogResource;

class ListSystemLogs extends ListRecords
{
    protected static string $resource = SystemLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
