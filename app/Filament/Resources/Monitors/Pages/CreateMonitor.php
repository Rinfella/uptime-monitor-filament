<?php

namespace App\Filament\Resources\Monitors\Pages;

use App\Filament\Resources\Monitors\MonitorResource;
use App\Services\HeartbeatCheckService;
use Filament\Resources\Pages\CreateRecord;

class CreateMonitor extends CreateRecord
{
    protected static string $resource = MonitorResource::class;

    protected function afterCreate(): void
    {
        $heartbeatCheckService = app(HeartbeatCheckService::class);
        $heartbeatCheckService->checkMonitor($this->record);
    }
}
