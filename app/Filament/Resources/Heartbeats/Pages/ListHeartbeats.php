<?php

namespace App\Filament\Resources\Heartbeats\Pages;

use App\Filament\Resources\Heartbeats\HeartbeatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHeartbeats extends ListRecords
{
    protected static string $resource = HeartbeatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
