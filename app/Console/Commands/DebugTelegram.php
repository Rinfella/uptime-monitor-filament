<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DebugTelegram extends Command
{
    protected $signature = 'telegram:debug';
    protected $description = 'Debug Telegram configuration and get chat ID';

    public function handle(): int
    {
        $this->info('=== Telegram Configuration Debug ===');
        $this->line('');

        // Check .env values
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $this->line('1. Environment Variables (.env):');
        $this->line('   TELEGRAM_BOT_TOKEN: ' . ($botToken ? '✓ Set' : '✗ Not set'));
        $this->line('   TELEGRAM_CHAT_ID: ' . ($chatId ? '✓ Set' : '✗ Not set'));
        $this->line('');

        // Check config values
        $configBotToken = config('services.telegram.bot_token');
        $configChatId = config('services.telegram.chat_id');

        $this->line('2. Config Values (config/services.php):');
        $this->line('   bot_token: ' . ($configBotToken ? '✓ Set' : '✗ Not set'));
        $this->line('   chat_id: ' . ($configChatId ? '✓ Set' : '✗ Not set'));
        $this->line('');

        if (!$configBotToken) {
            $this->error('Bot token not found in config!');
            $this->warn('Run: php artisan config:clear');
            return self::FAILURE;
        }

        $this->line('3. Bot Token (masked): ' . \Illuminate\Support\Str::mask($configBotToken, '*', 15, -10));
        $this->line('4. Chat ID: ' . ($configChatId ?? 'Not set'));
        $this->line('');

        // Test bot token validity
        $this->info('Testing bot token...');

        try {
            $response = Http::get("https://api.telegram.org/bot{$configBotToken}/getMe");

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✓ Bot token is valid!');
                $this->line('   Bot Name: ' . $data['result']['first_name']);
                $this->line('   Bot Username: @' . $data['result']['username']);
                $this->line('');
            } else {
                $this->error('✗ Bot token is invalid!');
                $this->line('   Status: ' . $response->status());
                $this->line('   Response: ' . $response->body());
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to validate bot token: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Get updates to find chat ID
        $this->info('Fetching recent messages to find chat ID...');

        try {
            $response = Http::get("https://api.telegram.org/bot{$configBotToken}/getUpdates");

            if ($response->successful()) {
                $data = $response->json();

                if (empty($data['result'])) {
                    $this->warn('No messages found!');
                    $this->line('');
                    $this->warn('To get your chat ID:');
                    $this->line('1. Open Telegram and search for your bot');
                    $this->line('2. Send any message to the bot (e.g., "Hello")');
                    $this->line('3. Run this command again: php artisan telegram:debug');
                    $this->line('');
                } else {
                    $this->info('✓ Found messages!');
                    $this->line('');

                    $chatIds = [];
                    foreach ($data['result'] as $update) {
                        if (isset($update['message']['chat']['id'])) {
                            $chatId = $update['message']['chat']['id'];
                            $chatType = $update['message']['chat']['type'];
                            $chatName = $update['message']['chat']['first_name'] ??
                                $update['message']['chat']['title'] ?? 'Unknown';

                            if (!in_array($chatId, $chatIds)) {
                                $chatIds[] = $chatId;
                                $this->line("Chat ID: {$chatId}");
                                $this->line("  Name: {$chatName}");
                                $this->line("  Type: {$chatType}");
                                $this->line('');
                            }
                        }
                    }

                    if (!empty($chatIds)) {
                        $this->info('Add this to your .env file:');
                        $this->line('TELEGRAM_CHAT_ID=' . $chatIds[0]);
                        $this->line('');

                        if ($configChatId && $configChatId != $chatIds[0]) {
                            $this->warn("Current TELEGRAM_CHAT_ID ({$configChatId}) doesn't match found chat ID ({$chatIds[0]})");
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to fetch updates: ' . $e->getMessage());
        }

        $this->line('');
        $this->info('Debug complete!');

        return self::SUCCESS;
    }
}
