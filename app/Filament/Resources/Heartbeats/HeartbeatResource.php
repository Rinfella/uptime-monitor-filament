<?php

namespace App\Filament\Resources\Heartbeats;

use App\Filament\Resources\Heartbeats\Pages\CreateHeartbeat;
use App\Filament\Resources\Heartbeats\Pages\EditHeartbeat;
use App\Filament\Resources\Heartbeats\Pages\ListHeartbeats;
use App\Filament\Resources\Heartbeats\Pages\ViewHeartbeat;
use App\Filament\Resources\Heartbeats\Schemas\HeartbeatForm;
use App\Filament\Resources\Heartbeats\Schemas\HeartbeatInfolist;
use App\Filament\Resources\Heartbeats\Tables\HeartbeatsTable;
use App\Models\Heartbeat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HeartbeatResource extends Resource
{
    protected static ?string $model = Heartbeat::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static ?string $recordTitleAttribute = 'heartbeat';

    public static function form(Schema $schema): Schema
    {
        return HeartbeatForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HeartbeatInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeartbeatsTable::configure($table);
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
            'index' => ListHeartbeats::route('/'),
            'create' => CreateHeartbeat::route('/create'),
            'view' => ViewHeartbeat::route('/{record}'),
            'edit' => EditHeartbeat::route('/{record}/edit'),
        ];
    }
}
