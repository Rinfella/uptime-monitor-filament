<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('heartbeats:check')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('heartbeats:cleanup --days=60')
    ->weekly()
    ->onlyOnSundays()
    ->at('03:00')
    ->runInBackground();
