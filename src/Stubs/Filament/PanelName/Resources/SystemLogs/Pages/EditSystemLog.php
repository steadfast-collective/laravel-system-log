<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\SystemLogResource;

class EditSystemLog extends EditRecord
{
    protected static string $resource = SystemLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
