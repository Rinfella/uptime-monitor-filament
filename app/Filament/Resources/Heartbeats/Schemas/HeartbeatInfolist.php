<?php

namespace App\Filament\Resources\Heartbeats\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HeartbeatInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('monitor.name')
                    ->label('Monitor'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'up' => 'success',
                        'down' => 'danger',
                        'unknown' => 'warning',
                        default => 'gray',
                    }),
                TextEntry::make('http_status_code')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('response_time')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('error_message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('checked_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
