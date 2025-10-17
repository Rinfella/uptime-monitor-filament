<?php

namespace App\Filament\Resources\Monitors;

use App\Filament\Resources\Monitors\Pages\CreateMonitor;
use App\Filament\Resources\Monitors\Pages\EditMonitor;
use App\Filament\Resources\Monitors\Pages\ListMonitors;
use App\Filament\Resources\Monitors\Pages\ViewMonitor;
use App\Filament\Resources\Monitors\Schemas\MonitorForm;
use App\Filament\Resources\Monitors\Schemas\MonitorInfolist;
use App\Filament\Resources\Monitors\Tables\MonitorsTable;
use App\Models\Monitor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MonitorResource extends Resource
{
    protected static ?string $model = Monitor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'Monitor';
    protected static ?string $navigationLabel = 'Monitors';
    protected static ?string $pluralModelLabel = 'Monitors';
    protected static ?string $modelLabel = 'Monitor';

    public static function form(Schema $schema): Schema
    {
        return MonitorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MonitorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MonitorsTable::configure($table);
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
            'index' => ListMonitors::route('/'),
            'create' => CreateMonitor::route('/create'),
            'view' => ViewMonitor::route('/{record}'),
            'edit' => EditMonitor::route('/{record}/edit'),
        ];
    }
}
