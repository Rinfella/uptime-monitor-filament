<?php

namespace App\Filament\Resources\Heartbeats\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeartbeatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('checked_at', 'desc')
            ->columns([
                TextColumn::make('monitor.name')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn($state) => match ($state) {
                        'up' => 'success',
                        'down' => 'danger',
                        'unknown' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('http_status_code')
                    ->label('HTTP Status')
                    ->sortable()
                    ->placeholder('N/A')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === null => 'gray',
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'info',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('response_time')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('checked_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
