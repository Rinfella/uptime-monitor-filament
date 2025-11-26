<?php

namespace App\Filament\Resources\Monitors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class MonitorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Monitor Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Monitor Name')
                            ->placeholder('e.g. My Server')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('url')
                            ->label('Monitor URL')
                            ->placeholder('e.g. https://example.com')
                            ->url()
                            ->maxLength(2048)
                            ->required()
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ])
                    ->columns(2),

                Section::make('Interval Settings')
                    ->schema([
                        TextInput::make('check_interval_minutes')
                            ->label('Check Interval (minutes)')
                            ->placeholder('e.g. 5')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1440)
                            ->required()
                            ->default(2)
                            ->suffixIcon('heroicon-m-clock'),

                        TextInput::make('max_consecutive_failures')
                            ->label('Max Consecutive Failures')
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                    ])
                    ->columns(2),

                Section::make('Notification Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),

                        Toggle::make('notify_on_failure')
                            ->default(true)
                            ->label('Notify on Failure'),

                        Toggle::make('notify_on_recovery')
                            ->default(true)
                            ->label('Notify on Recovery'),

                    ])
                    ->columns(1),

                Section::make('SSL Settings')
                    ->schema([
                        Toggle::make('check_ssl_certificate')
                            ->default(true)
                            ->label('Notify SSL Cert. expiry'),

                        TextEntry::make('ssl_certificate_expires_at')
                            ->label('SSL Certificate Expiry Date')
                            ->formatStateUsing(fn($state) => $state ?
                                Carbon::parse($state)->toDayDateTimeString() : 'N/A')
                            ->disabled(),
                    ])
                    ->columns(2)

            ]);
    }
}
