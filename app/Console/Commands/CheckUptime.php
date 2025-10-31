<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use App\Services\UptimeCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckUptime extends Command
{
    protected $signature = 'uptime:check { --monitor_id= : ID of a specific monitor to check }';
    protected $description = 'Check uptime of all listed monitors';

    public function handle(UptimeCheckService $uptimeCheckService): int
    {
        $this->info('Starting uptime checks..');

        if ($monitorId = $this->option('monitor_id')) {
            $monitor = Monitor::find($monitorId);

            if (!$monitor) {
                $this->error("Monitor with ID {$monitorId} not found.");
                return self::FAILURE;
            }

            $this->info('Checking monitor: . {$monitor->name}');
            $result = $uptimeCheckService->checkMonitor($monitor);

            $this->displayResult($monitor->name, $result);
            return self::SUCCESS;
        }

        // Check all monitors
        $results = $uptimeCheckService->checkAllMonitors();

        if (empty($results)) {
            $this->warn('No monitors found that need checking.');
            return self::SUCCESS;
        }

        $this->info('Check completed for ' . count($results) . ' monitor(s).');

        // Display results in a table
        $tableData = [];
        foreach ($results as $result) {
            $tableData[] = [
                'Monitor' => $result['monitor'],
                'Status' => $result['result']['is_up'] ? 'UP' : 'DOWN',
                'Response Time' => $result['result']['response_time'] . 'ms',
                'Status Code' => $result['result']['status_code'] ?? 'N/A',
                'Error' => $result['result']['error_message']
                    ? Str::limit($result['result']['error_message'], 50) : '-',
            ];
        }

        $this->table(
            ['monitor', 'Status', 'Response Time', 'Status Code', 'Error'],
            $tableData
        );

        return self::SUCCESS;
    }

    private function displayResult(string $name, array $result): void
    {
        $this->line('');
        $this->line("Results for: {$name}");
        $this->line('─────────────────────────────────────');

        if ($result['is_up']) {
            $this->info('Status: ✅ UP');
        } else {
            $this->error('Status: ❌ DOWN');
        }

        $this->line("Response Time: {$result['response_time']}ms");
        $this->line('Status Code: ' . ($result['status_code'] ?? 'N/A'));

        if ($result['error_message']) {
            $this->line("Error Message: {$result['error_message']}");
        }

        $this->line('─────────────────────────────────────');
    }
}
