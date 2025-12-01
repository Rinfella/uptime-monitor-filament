<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('heartbeats:check')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('heartbeats:cleanup --days=14')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->runInBackground();

Schedule::command('check:certificates')
    ->daily()
    ->runInBackground();
