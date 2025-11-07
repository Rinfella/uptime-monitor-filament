<?php

namespace App\Filament\Resources\Heartbeats\Pages;

use App\Filament\Resources\Heartbeats\HeartbeatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeartbeat extends CreateRecord
{
    protected static string $resource = HeartbeatResource::class;
}
