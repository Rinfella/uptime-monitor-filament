<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class SslCertificateExpiresSoon extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Monitor $monitor,
        public int $daysRemaining,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram($notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->token(config('services.telegram.bot_token'))
            ->to(config('services.telegram.chat_id'))
            ->content(
                "⚠️ SSL certificate Expiry Warnning ⚠️\n\n" .
                    "**Site:** {$this->monitor->name}\n" .
                    "**URL:** {$this->monitor->url}\n" .
                    "**Expiry Date:** {$this->monitor->ssl_certificate_expires_at->format('Y-m-d')}\n" .
                    "**Days Remaining:** {$this->daysRemaining} days\n\n" .
                    "Please renew the SSL certificate before it expires."
            )
            ->options(['parse_mode' => 'Markdown']);
    }
}
