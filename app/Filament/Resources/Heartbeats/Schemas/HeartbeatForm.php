<?php

namespace App\Filament\Resources\Heartbeats\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HeartbeatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('monitor_id')
                    ->relationship('monitor', 'name')
                    ->required(),
                Select::make('status')
                    ->options(['up' => 'Up', 'down' => 'Down', 'unknown' => 'Unknown'])
                    ->default('unknown')
                    ->required(),
                TextInput::make('http_status_code')
                    ->numeric()
                    ->default(null),
                TextInput::make('response_time')
                    ->numeric()
                    ->default(null),
                Textarea::make('error_message')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('checked_at')
                    ->required(),
            ]);
    }
}
