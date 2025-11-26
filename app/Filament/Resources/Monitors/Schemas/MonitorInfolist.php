<?php

namespace App\Filament\Resources\Monitors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MonitorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Monitor Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Monitor Name'),
                        TextEntry::make('url')
                            ->label('URL')
                            ->url(fn($record) => $record->url)
                            ->openUrlInNewTab(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'up' => 'success',
                                'down' => 'danger',
                                'unknown' => 'warning',
                                default => 'gray',
                            }),
                    ])->columns(3),

                Section::make('SSL Certificate')
                    ->schema([
                        IconEntry::make('check_ssl_certificate')
                            ->label('Notfy on Expiry')
                            ->boolean(),

                        TextEntry::make('ssl_certificate_expires_at')
                            ->label('Expires At')
                            ->dateTime('M j, Y H:i')
                            ->placeholder('N/A'),
                    ])
                    ->visible(fn($record) => $record->check_ssl_certificate)
                    ->columns(2),
            ]);
    }
}
