<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\SystemLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\SystemLogResource;

class ViewSystemLog extends ViewRecord
{
    protected static string $resource = SystemLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
