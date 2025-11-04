<?php

namespace Database\Seeders;

use App\Models\Monitor;
use Illuminate\Database\Seeder;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Monitor::insert([
            [
                'name' => 'Example Monitor',
                'url' => 'https://example.com',
                'check_interval_minutes' => 5,
                'notify_on_failure' => true,
                'notify_on_recovery' => true,
                'consecutive_failures' => 0,
                'is_active' => true,
            ],

            [
                'name' => 'Example2 Monitor',
                'url' => 'https://example2.com',
                'check_interval_minutes' => 10,
                'notify_on_failure' => false,
                'notify_on_recovery' => true,
                'consecutive_failures' => 0,
                'is_active' => true,
            ],

            [
                'name' => 'Example Error Monitor',
                'url' => 'https://lerisa.in',
                'check_interval_minutes' => 2,
                'notify_on_failure' => true,
                'notify_on_recovery' => false,
                'consecutive_failures' => 0,
                'is_active' => false,
            ]

        ]);
    }
}
