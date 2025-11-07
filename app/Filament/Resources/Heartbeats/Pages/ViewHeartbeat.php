<?php

namespace App\Filament\Resources\Heartbeats\Pages;

use App\Filament\Resources\Heartbeats\HeartbeatResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHeartbeat extends ViewRecord
{
    protected static string $resource = HeartbeatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
