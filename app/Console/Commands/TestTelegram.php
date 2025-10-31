<?php

namespace App\Console\Commands;

use App\Notifications\TelegramTestMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TestTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test {--message= : Custom test message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test Telegram notification';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $message = $this->option('message') ?? 'This is a test notification from your uptime monitor!';

        // Check if Telegram credentials are configured
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        $this->info('=== Telegram Configuration Debug ===');
        $this->line('Bot Token from config: ' . ($botToken ? '✓ Found' : '✗ Not found'));
        $this->line('Chat ID from config: ' . ($chatId ? '✓ Found' : '✗ Not found'));
        $this->line('');

        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN is not configured in your .env file');
            $this->line('');
            $this->warn('Add this to your .env file:');
            $this->line('TELEGRAM_BOT_TOKEN=your_bot_token_here');
            return self::FAILURE;
        }

        if (!$chatId) {
            $this->error('❌ TELEGRAM_CHAT_ID is not configured in your .env file');
            $this->line('');
            $this->warn('Add this to your .env file:');
            $this->line('TELEGRAM_CHAT_ID=your_chat_id_here');
            return self::FAILURE;
        }

        $this->info('Sending test notification to Telegram...');
        $this->info('Bot Token: ' . \Illuminate\Support\Str::mask($botToken, '*', 10, -5));
        $this->info('Chat ID: ' . $chatId);
        $this->line('');

        try {
            Notification::route('telegram', $chatId)
                ->notify(new TelegramTestMessage($message));

            $this->info('✅ Test notification sent successfully!');
            $this->info('Check your Telegram chat to verify delivery.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send notification: ' . $e->getMessage());
            $this->line('');
            $this->warn('Troubleshooting tips:');
            $this->line('1. Verify your TELEGRAM_BOT_TOKEN is correct');
            $this->line('2. Make sure your TELEGRAM_CHAT_ID is correct');
            $this->line('3. Ensure the bot has been added to the chat');
            $this->line('4. Check if the bot has permission to send messages');
            $this->line('5. Try running: php artisan config:clear');

            return self::FAILURE;
        }
    }
}
