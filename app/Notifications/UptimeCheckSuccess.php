<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class UptimeCheckSuccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Monitor $monitor,
        public bool $isRecovery = false,
        public bool $isInitialCheck = false,
    ) {}


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $responseTime = $this->monitor->response_time;
        $statusCode = $this->monitor->http_status_code;

        $title = $this->isInitialCheck ? '✅ NEW MONITOR ADDED' : '🟢 SITE IS UP';

        $message = "**{$title}**\n\n";
        $message .= "**Site:** {$this->monitor->name}\n";
        $message .= "**URL:** {$this->monitor->url}\n";
        $message .= "**Status:** UP\n";

        if ($responseTime) {
            $message .= "**Response Time:** {$responseTime} ms\n";
        }

        if ($statusCode) {
            $message .= "**HTTP Status Code:** {$statusCode}\n";
        }

        if ($this->isInitialCheck) {
            $message .= "\nThis monitor has been successfully added and its initial status is UP.";
        } elseif ($this->isRecovery) {
            $message .= "\nThe site has recovered and is now back online.";
        } else {
            $message .= "\nThe site is operational.";
        }

        $message .= "\n\n_" . now()->format('M j, Y g:i A') . "_";

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
