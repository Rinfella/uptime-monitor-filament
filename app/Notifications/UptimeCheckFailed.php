<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class UptimeCheckFailed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Monitor $monitor
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toTelegram($notifiable): TelegramMessage
    {
        $responseTime = $this->monitor->getResponseTime();
        $errorMessage = $this->monitor->getErrorMessage();
        $statusCode = $this->monitor->getHttpStatusCode();

        $message = "🔴 **SITE DOWN ALERT**\n\n";
        $message .= "**Site:** {$this->monitor->name}\n";
        $message .= "**URL:** {$this->monitor->url}\n";
        $message .= "**Status:** Down\n";
        $message .= "**Consecutive Failures:** {$this->monitor->consecutive_failures}\n";

        if ($errorMessage) {
            $errorPreview = strlen($errorMessage) > 200
                ? substr($errorMessage, 0, 200) . '...'
                : $errorMessage;
            $message .= "**Error Message:** {$errorPreview}\n";
        }

        if ($responseTime) {
            $message .= "**Response Time:** {$responseTime}ms\n";
        }

        $message .= "\nPlease check the server immediately.";

        return TelegramMessage::create()
            ->token(config('services.telegram.bot_token'))
            ->to(config('services.telegram.chat_id'))
            ->content($message)
            ->options([
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
    }
}
