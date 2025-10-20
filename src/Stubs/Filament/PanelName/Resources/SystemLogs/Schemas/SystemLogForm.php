<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\SystemLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SystemLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('internal_type'),
                TextInput::make('internal_id'),
                TextInput::make('external_type'),
                TextInput::make('external_id'),
                TextInput::make('log_level'),
                TextInput::make('message'),
                TextInput::make('context'),
                TextInput::make('notes'),
                TextInput::make('retried_at'),
                TextInput::make('retried_by'),
                TextInput::make('resolved'),
            ]);
    }
}
