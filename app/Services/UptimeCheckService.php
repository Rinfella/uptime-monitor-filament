<?php

namespace App\Services;

use App\Models\Monitor;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UptimeCheckRecovered;

class UptimeCheckService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function checkMonitor(Monitor $monitor): array
    {
        $startTime = microtime(true);

        try {
            Log::info("Checking uptime for: {$monitor->name} ({$monitor->url})");

            $response = Http::timeout(config('services.uptime-monitor.http_timeout', 10))
                ->get($monitor->url);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);

            $isUp = $response->successful();
            $statusCode = $response->status();

            $result = [
                'is_up' => $isUp,
                'response_time' => $responseTime,
                'status_code' => $statusCode,
                'error_message' => null,
            ];

            if ($isUp) {
                $this->handleSuccessfulCheck($monitor, $result);
            } else {
                $result['error_message'] = "HTTP {$statusCode}: " . $response->body();
                $this->handleFailedCheck($monitor, $result);
            }

            return $result;
        } catch (ConnectionException $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);

            $result = [
                'is_up' => false,
                'response_time' => $responseTime,
                'status_code' => null,
                'error_message' => 'Connection failed: ' . $e->getMessage(),
            ];

            $this->handleFailedCheck($monitor, $result);
            return $result;
        } catch (RequestException $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);

            $result = [
                'is_up' => false,
                'response_time' => $responseTime,
                'status_code' => null,
                'error_message' => 'Request error: ' . $e->getMessage(),
            ];

            $this->handleFailedCheck($monitor, $result);
            return $result;
        } catch (\Exception $e) {
            Log::error("Unexpected error checking monitor {$monitor->name}: " . $e->getMessage());

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);

            $result = [
                'is_up' => false,
                'response_time' => $responseTime,
                'status_code' => null,
                'error_message' => 'Unexpected error: ' . $e->getMessage(),
            ];

            $this->handleFailedCheck($monitor, $result);
            return $result;
        }
    }

    private function handleSuccessfulCheck(Monitor $monitor, array $result): void
    {
        $wasDown = $monitor->status === 'down';

        $monitor->update([
            'status' => 'up',
            'last_checked_at' => now(),
            'last_up_at' => now(),
            'response_time' => $result['response_time'],
            'http_status_code' => $result['status_code'],
            'error_message' => null,
            'consecutive_failures' => 0,
        ]);

        if ($wasDown && $monitor->notify_on_recovery) {
            // Trigger recovery notification
            $this->sendRecoveryNotification($monitor);
        }

        Log::info("Monitor {$monitor->name} is up. Respinse time: ({$result['response_time']}ms)");
    }

    private function handleFailedCheck(Monitor $monitor, array $result): void
    {
        $consecutiveFailures = $monitor->consecutive_failures + 1;
        $wasUp = $monitor->status === 'up';

        $monitor->update([
            'status' => 'down',
            'last_checked_at' => now(),
            'last_down_at' => now(),
            'response_time' => $result['response_time'],
            'http_status_code' => $result['status_code'],
            'error_message' => $result['error_message'],
            'consecutive_failures' => $consecutiveFailures,
        ]);

        if (($wasUp || $consecutiveFailures >= $monitor->max_consecutive_failures) && $monitor->notify_on_failure) {
            // Trigger failure notification
            $this->sendFailureNotification($monitor);
        }

        Log::warning("Monitor {$monitor->name} is down. Error: {$result['error_message']}");
    }

    private function sendFailureNotification(Monitor $monitor): void
    {
        try {
            Notification::route('telegram', config('services.telegram.chat_id'))
                ->notify(new UptimeCheckRecovered($monitor));
        } catch (\Exception $e) {
            Log::error("Failed to send notification for {$monitor->name}: " . $e->getMessage());
        }
    }

    public function checkAllMonitors(): array
    {
        $monitors = Monitor::needsCheck()->get();
        $results = [];

        Log::info('Starting uptime checks for ' . $monitors->count() . ' monitors.');

        foreach ($monitors as $monitor) {
            $results[] = [
                'monitor' => $monitor->name,
                'result' => $this->checkMonitor($monitor)
            ];
        }

        Log::info("Completed uptime checks for " . count($results) . " monitors.");

        return $results;
    }

    public function sendRecoveryNotification(Monitor $monitor): void
    {
        try {
            Notification::route('telegram', config('services.telegram.chat_id'))
                ->notify(new UptimeCheckRecovered($monitor));
        } catch (\Exception $e) {
            Log::error("Failed to send notification for {$monitor->name}: " . $e->getMessage());
        }
    }
}
