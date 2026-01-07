<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Tables;

use App\Models\SystemLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use SteadfastCollective\LaravelSystemLog\Tables\Filters\RangeFilter;

class SystemLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    // TODO: Format the date to your applications timezone
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->default('-')
                    ->sortable(),
                TextColumn::make('message')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->default('-')
                    ->wrap()
                    ->lineClamp(1),
                TextColumn::make('internal_type')
                    ->label('Internal Type')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable()
                    ->default('-'),
                TextColumn::make('internal_id')
                    ->label('Internal ID')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable()
                    ->default('-'),
                TextColumn::make('external_type')
                    ->label('External Type')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable()
                    ->default('-'),
                TextColumn::make('external_id')
                    ->label('External ID')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->sortable()
                    ->default('-'),
                TextColumn::make('log_level')
                    ->badge()
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->default('-')
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'gray',
                        'alert' => 'warning',
                        'notice' => 'success',
                        'error' => 'danger',
                        'debug' => 'primary',
                        'emergency' => 'danger',
                        'critical' => 'danger',
                        'warning' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('notes')
                    ->wrap()
                    ->default('-')
                    ->searchable(isIndividual: true, isGlobal: true)
                    ->lineClamp(1),
                ToggleColumn::make('resolved')
                    ->label('Resolved')
                    ->sortable(),
            ])
            ->filters([
                // Time Filter/Created At Filter
                RangeFilter::make('created_at')
                    ->using(DatePicker::class, 'Time'),
                SelectFilter::make('code')
                    ->multiple()
                    ->label('Code')
                    ->options(function () {
                        $options = SystemLog::distinct('code')->pluck('code')->filter()->toArray();

                        return array_combine($options, $options);
                    }),
                SelectFilter::make('internal_type')
                    ->multiple()
                    ->label('External Type')
                    ->options(function () {
                        // raw query the SystemLog table to get the distinct values of the internal_type column (cache it into a variable, then return it)
                        $options = Cache::remember('system-log-internal-type-options', now()->addDay(), function () {
                            return SystemLog::distinct('internal_type')->pluck('internal_type')->filter()->toArray();
                        });

                        return array_combine($options, $options);
                    }),
                SelectFilter::make('external_type')
                    ->label('External Type')
                    ->multiple()
                    ->options(function () {
                        // raw query the SystemLog table to get the distinct values of the external_type column (cache it into a variable, then return it)
                        $options = Cache::remember('system-log-external-type-options', now()->addDay(), function () {
                            return SystemLog::distinct('external_type')->pluck('external_type')->filter()->toArray();
                        });

                        return array_combine($options, $options);
                    }),
                TernaryFilter::make('resolved')
                    ->label('Resolved'),
                SelectFilter::make('log_level')
                    ->multiple()
                    ->label('Level')
                    ->options([
                        'info' => 'Info',
                        'alert' => 'Alert',
                        'notice' => 'Notice',
                        'error' => 'Error',
                        'debug' => 'Debug',
                        'emergency' => 'Emergency',
                        'critical' => 'Critical',
                        'warning' => 'Warning',
                    ]),
            ], layout: FiltersLayout::Modal)
            ->groups([
                'internal_type',
                'internal_id',
                'external_type',
                'external_id',
                'log_level',
                'resolved',
                'message',
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(function (Builder $query, string $direction): Builder {
                return $query->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc');
            })
            ->poll('5s');
    }
}
