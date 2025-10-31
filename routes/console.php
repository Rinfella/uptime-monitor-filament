<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('uptime:check')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
