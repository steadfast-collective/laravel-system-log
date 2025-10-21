<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Schemas;

use App\Models\SystemLog;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

class SystemLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('internal_type')
                    ->state(fn (SystemLog $record): string => $record->internal_type ?? ''),
                TextEntry::make('internal_id')
                    ->state(fn (SystemLog $record): string => $record->internal_id ?? ''),
                TextEntry::make('external_type')
                    ->state(fn (SystemLog $record): string => $record->external_type ?? ''),
                TextEntry::make('external_id')
                    ->state(fn (SystemLog $record): string => $record->external_id ?? ''),
                TextEntry::make('log_level')
                    ->state(fn (SystemLog $record): string => $record->log_level ?? ''),
                TextEntry::make('message')
                    ->state(fn (SystemLog $record): string => $record->message ?? ''),
                KeyValueEntry::make('flat-context')
                    ->label('Context')
                    ->default(function (SystemLog $systemLog) {
                        return Arr::dot($systemLog->context ?? []);
                    })
                    ->valueLabel('Info'),
                TextArea::make('notes'),
                Toggle::make('resolved'),
                TextEntry::make('created_at')
                    // TODO: Format the date to your applications timezone
                    ->state(fn (SystemLog $record): string => $record->created_at ?? ''),
                TextEntry::make('retried_at')
                    // TODO: Format the date to your applications timezone
                    ->state(fn (SystemLog $record): string => $record->retried_at ?? ''),
                TextEntry::make('retryingUser.name')
                    ->label('Retrying User')
                    ->state(fn (SystemLog $record): string => $record->retryingUser->name ?? ''),
            ]);
    }
}
