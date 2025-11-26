<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Monitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SslCertificateExpiresSoon;

class CheckSslCertificates extends Command
{
    protected $signature = 'check:certificates';
    protected $description = 'Command to check expiry dates of SSL certificates for monitors';

    public function handle(): int
    {
        $monitors = Monitor::where('check_ssl_certificate', true)
            ->where('is_active', true)
            ->get();

        foreach ($monitors as $monitor) {
            $this->checkMonitor($monitor);
        }

        $this->info("Checked {$monitors->count()} monitors.");
        return self::SUCCESS;
    }

    private function checkMonitor(Monitor $monitor): void
    {
        $url = parse_url($monitor->url, PHP_URL_HOST);

        if (!$url) return;

        try {
            $context = stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]);

            $client = @stream_socket_client(
                "ssl://$url:443",
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$client) {
                Log::warning(
                    "Could not connect to {$url} for SSL check: $errstr ($errno)"
                );
                return;
            }

            $params = stream_context_get_params($client);
            $cert = $params["options"]["ssl"]["peer_certificate"];
            $certInfo = openssl_x509_parse($cert);

            if (isset($certInfo['validTo_time_t'])) {
                $validTo = Carbon::createFromTimestamp($certInfo['validTo_time_t']);
                $monitor->update(['ssl_certificate_expires_at' => $validTo]);

                $daysRemaining = now()->diffInDays($validTo, false);

                // Notify if expiring in 7, 3 or 1 day(s)
                if ($monitor->check_ssl_certificate && $daysRemaining <= 7 && $daysRemaining > 0) {
                    // Check if we haven't already notified today
                    Notification::route('telegram', config('services.telegram.chat_id'))
                        ->notify(new SslCertificateExpiresSoon($monitor, (int)$daysRemaining));
                }
            }
        } catch (\Exception $e) {
            Log::error("SSL Check failed for {$monitor->url}: " . $e->getMessage());
        }
    }
}
