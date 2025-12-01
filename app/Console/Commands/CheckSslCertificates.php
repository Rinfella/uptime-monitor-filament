<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use Illuminate\Console\Command;
use App\Services\SslCheckService;

class CheckSslCertificates extends Command
{
    protected $signature = 'check:certificates { --monitor_id= : ID of the monitor to check SSL for }';
    protected $description = 'Command to check expiry dates of SSL certificates for monitors';

    public function handle(SslCheckService $sslService): int
    {
        $monitorId = $this->option('monitor_id');

        if ($monitorId) {
            // Check a specific monitor
            $monitor = Monitor::find($monitorId);
            if ($monitor && $monitor->is_active && $monitor->check_ssl_certificate) {
                $sslService->checkSsl($monitor);
                $this->info("Checked SSL for monitor: {$monitor->name}");
            }
        } else {
            // Check all monitors with SSL checking enabled
            $monitors = Monitor::where('check_ssl_certificate', true)
                ->where('is_active', true)
                ->get();

            foreach ($monitors as $monitor) {
                $sslService->checkSsl($monitor);
            }
            $this->info("Checked {$monitors->count()} monitors.");
        }

        return self::SUCCESS;
    }
}
