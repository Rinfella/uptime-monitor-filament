<?php

namespace App\Filament\Resources\Monitors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MonitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'success' => 'up',
                        'danger' => 'down',
                        'warning' => 'unknown',
                    ])
                    ->icons([
                        'heroicon-m-check-circle' => 'up',
                        'heroicon-m-x-circle' => 'down',
                        'heroicon-m-question-mark-circle' => 'unknown',
                    ])
                    ->sortable(),

                TextColumn::make('response_time')
                    ->label('Response Time')
                    ->suffix(' ms')
                    ->sortable()
                    ->placeholder('N/A')
                    ->color(fn($state) => match (true) {
                        $state === null => 'gray',
                        $state < 200 => 'success',
                        $state < 5000 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('http_status_codes')
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

                TextColumn::make('last_checked_at')
                    ->label('Last Checked')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('Never')
                    ->description(fn($record) => $record->last_checked_at ? $record->last_checked_at->diffForHumans() : null),

                TextColumn::make('check_interval_minutes')
                    ->label('Check Interval')
                    ->suffix(' min(s)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('consecutive_failures')
                    ->label('Consecutive Failures')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === 0 => 'success',
                        $state > 0 && $state < 3 => 'warning',
                        $state >= 3 => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'up' => 'Up',
                        'down' => 'Down',
                        'unknown' => 'Unknown',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Active Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
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
            ->defaultSort('created_at', 'desc');
    }
}
