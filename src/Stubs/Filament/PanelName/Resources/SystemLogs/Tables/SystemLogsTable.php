<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\SystemLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SystemLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('internal_type')
                    ->searchable(),
                TextColumn::make('internal_id')
                    ->searchable(),
                TextColumn::make('external_type')
                    ->searchable(),
                TextColumn::make('external_id')
                    ->searchable(),
                TextColumn::make('log_level')
                    ->searchable(),
                TextColumn::make('message')
                    ->searchable(),
                TextColumn::make('notes')
                    ->searchable(),
                TextColumn::make('retried_at')
                    ->searchable(),
                TextColumn::make('retried_by')
                    ->searchable(),
                TextColumn::make('resolved')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
