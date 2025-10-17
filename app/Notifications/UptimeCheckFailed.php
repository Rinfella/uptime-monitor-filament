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
    public function __construct(public Monitor $monitor)
    {
        //
    }

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
        $downtime = $this->monitor->last_down_at
            ? $this->monitor->last_down_at->diffForHumans()
            : 'Unknown';

        $message = "🔴 **SITE DOWN ALERT**\n\n";
        $message .= "**Site:** {$this->monitor->name}\n";
        $message .= "**URL:** {$this->monitor->url}\n";
        $message .= "**Status:** Down\n";
        $message .= "**Downtime:** {$downtime}\n";
        $message .= "**Consecutive Failures:** {$this->monitor->consecutive_failures}\n";

        if ($this->monitor->error_message) {
            $message .= "**Error:** " . substr($this->monitor->error_message, 0, 200) . "\n";
        }

        if ($this->monitor->response_time) {
            $message .= "**Response Time:** {$this->monitor->response_time}ms\n";
        }

        $message .= "\nPlease check the server immediately.";

        return TelegramMessage::create()
            ->to(config('services.telegram.chat_id'))
            ->content($message)
            ->options([
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
