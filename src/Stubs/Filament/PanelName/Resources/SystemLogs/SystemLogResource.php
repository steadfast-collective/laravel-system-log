<?php

namespace SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs;

use App\Models\SystemLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Pages\EditSystemLog;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Pages\ListSystemLogs;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Schemas\SystemLogForm;
use SteadfastCollective\LaravelSystemLog\Stubs\Filament\PanelName\Resources\SystemLogs\Tables\SystemLogsTable;

class SystemLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SystemLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemLogs::route('/'),
            'edit' => EditSystemLog::route('/{record}/edit'),
        ];
    }
}
