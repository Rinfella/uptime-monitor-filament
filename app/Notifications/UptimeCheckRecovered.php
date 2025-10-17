<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class UptimeCheckRecovered extends Notification
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
        $uptime = $this->monitor->last_up_at
            ? $this->monitor->last_up_at->diffForHumans()
            : 'Now';

        $message = "🟢 **SITE RECOVERED**\n\n";
        $message .= "**Site:** {$this->monitor->name}\n";
        $message .= "**URL:** {$this->monitor->url}\n";
        $message .= "**Status:** Up\n";
        $message .= "**Uptime:** {$uptime}\n";
        $message .= "**Response Time:** {$this->monitor->response_time}ms\n";

        if ($this->monitor->http_status_code) {
            $message .= "**HTTP Status Code:** {$this->monitor->http_status_code}\n";
        }

        $message .= "\nYour site is back online..";

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
