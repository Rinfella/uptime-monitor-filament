<?php

namespace App\Services;

use App\Models\Monitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SslCertificateExpiresSoon;

class SslCheckService
{
    public function checkSsl(Monitor $monitor): void
    {
        $url = parse_url($monitor->url, PHP_URL_HOST);

        if (!$url) return;

        try {
            $context = stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => true,
                    "verify_peer_name" => true,
                ],
            ]);

            $client = @stream_socket_client(
                "ssl://{$url}:443",
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$client) {
                Log::warning("Could not connect to {$url}: $errstr ($errno)");
                return;
            }

            $params = stream_context_get_params($client);
            $cert = $params["options"]["ssl"]["peer_certificate"];
            $certInfo = openssl_x509_parse($cert);

            if (isset($certInfo['validTo_time_t'])) {
                $validTo = Carbon::createFromTimestamp($certInfo['validTo_time_t']);
                $monitor->update(['ssl_certificate_expires_at' => $validTo]);

                $daysRemaining = Carbon::now()->diffInDays($validTo, false);

                // Notify if expiring in 7, 3 or 1 day(s)
                if ($monitor->check_ssl_certificate && $daysRemaining <= 7 && $daysRemaining > 0) {
                    // Check if we haven't already notified today
                    Notification::route('telegram', config('services.telegram.chat_id'))
                        ->notify(new SslCertificateExpiresSoon($monitor, (int)$daysRemaining));
                }
            }
        } catch (\Exception $e) {
            Log::error("Error checking SSL for {$monitor->name}: " . $e->getMessage());
        }
    }
}
