<?php

namespace App\Filament\Resources\Heartbeats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->badge(),
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
                // ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
