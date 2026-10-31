<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Chaplaincy Scheduled Tasks
|--------------------------------------------------------------------------
| Requires the server cron entry: * * * * * php artisan schedule:run
*/

Schedule::command('chapel:reminders')->dailyAt('06:00')->timezone('Africa/Harare')->withoutOverlapping();
Schedule::command('chapel:backup')->dailyAt('02:00')->timezone('Africa/Harare')->withoutOverlapping();
