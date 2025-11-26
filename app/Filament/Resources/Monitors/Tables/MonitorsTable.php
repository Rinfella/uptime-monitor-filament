<?php

namespace App\Filament\Resources\Monitors\Tables;

use App\Models\Heartbeat;
use Illuminate\Database\Eloquent\Builder;
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
                    ->label('Monitor Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->url),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn($state) => strtoupper($state))
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

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('check_ssl_certificate')
                    ->label('Check SSL')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('ssl_certificate_expires_at')
                    ->label('SSL Expires At')
                    ->date('M j, Y')
                    ->placeholder('N/A')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === null => 'gray',
                        now()->diffInDays($state, false) < 0 => 'danger',
                        now()->diffInDays($state, false) <= 7 => 'danger',
                        now()->diffInDays($state, false) <= 30 => 'warning',
                        default => 'success',
                    })
                    ->description(fn($state) => $state ?
                        $state->diffForHumans() : 'Not Checked'),

                TextColumn::make('check_interval_minutes')
                    ->label('Check Interval')
                    ->suffix(' min(s)'),

                TextColumn::make('latestHeartbeat.checked_at')
                    ->label('Last Checked')
                    ->dateTime('M j, Y H:i')
                    ->placeholder('Never')
                    ->description(
                        fn($record) =>
                        $record->latestHeartbeat?->checked_at
                            ? $record->latestHeartbeat->checked_at->diffForHumans()
                            : null
                    )
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Heartbeat::select('checked_at')
                                ->whereColumn('monitor_id', 'monitors.id')
                                ->latest('checked_at')
                                ->limit(1),
                            $direction
                        );
                    }),

                TextColumn::make('check_interval_minutes')
                    ->label('Check Interval')
                    ->suffix(' min(s)')
                    ->sortable(),


                TextColumn::make('latestHeartbeat.response_time')
                    ->label('Response Time')
                    ->placeholder('N/A')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Heartbeat::select('response_time')
                                ->whereColumn('monitor_id', 'monitors.id')
                                ->latest('checked_at')
                                ->limit(1),
                            $direction
                        );
                    })
                    ->color(fn($state) => match (true) {
                        $state === null => 'gray',
                        $state < 2000 => 'success',
                        $state < 5000 => 'warning',
                        default => 'danger',
                    })
                    ->icon(fn($state) => match (true) {
                        $state === null => 'heroicon-m-question-mark-circle',
                        $state < 2000 => 'heroicon-m-bolt',
                        $state < 5000 => 'heroicon-m-clock',
                        default => 'heroicon-m-exclamation-triangle',
                    }),

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
