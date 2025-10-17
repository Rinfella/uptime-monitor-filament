<?php

namespace App\Filament\Resources\Monitors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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


            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
