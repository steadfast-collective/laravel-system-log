<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\SystemLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SystemLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('internal_type')
                    ->placeholder('-'),
                TextEntry::make('internal_id')
                    ->placeholder('-'),
                TextEntry::make('external_type')
                    ->placeholder('-'),
                TextEntry::make('external_id')
                    ->placeholder('-'),
                TextEntry::make('log_level')
                    ->placeholder('-'),
                TextEntry::make('message')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-'),
                TextEntry::make('retried_at')
                    ->placeholder('-'),
                TextEntry::make('retried_by')
                    ->placeholder('-'),
                TextEntry::make('resolved')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
