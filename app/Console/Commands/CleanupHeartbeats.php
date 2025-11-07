<?php

namespace App\Console\Commands;

use App\Models\Heartbeat;
use Illuminate\Console\Command;

class CleanupHeartbeats extends Command
{
    protected $signature = 'heartbeats:cleanup {--days=60 : The number of days of heartbeats to keep}';
    protected $description = 'Cleanup old heartbeat records';

    public function handle(): int
    {
        $days = $this->option('days');
        $this->info("Cleaning up heartbeats older than {$days} days...");

        $deleted = Heartbeat::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} old heartbeat records.");

        return self::SUCCESS;
    }
}
