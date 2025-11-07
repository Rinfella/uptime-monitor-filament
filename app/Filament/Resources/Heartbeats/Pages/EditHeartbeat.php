<?php

namespace App\Filament\Resources\Heartbeats\Pages;

use App\Filament\Resources\Heartbeats\HeartbeatResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHeartbeat extends EditRecord
{
    protected static string $resource = HeartbeatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
