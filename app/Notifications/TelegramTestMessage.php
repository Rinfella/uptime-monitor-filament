<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramTestMessage extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $testMessage = 'This is a test message from uptime monitor application.')
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
        $message = "📢 **TEST MESSAGE**\n\n";
        $message .= $this->testMessage . "\n\n";
        $message .= "If you received this message, your Telegram notifications are working correctly.";
        $message .= "**Sent at:**" . now()->toDateTimeString() . "\n";
        $message .= "**From:**" . config('app.name') . "\n";

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
