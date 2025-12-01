<?php

namespace App\Filament\Resources\Monitors\Pages;

use App\Filament\Resources\Monitors\MonitorResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Artisan;

class CreateMonitor extends CreateRecord
{
    protected static string $resource = MonitorResource::class;

    protected function afterCreate(): void
    {
        Artisan::queue('heartbeats:check', [
            '--monitor_id' => $this->record->id,
        ]);

        if ($this->record->check_ssl_certificate) {
            Artisan::queue('check:certificates', [
                '--monitor_id' => $this->record->id,
            ]);
        }
    }
}
