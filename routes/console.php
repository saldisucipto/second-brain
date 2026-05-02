<?php

use App\Services\ActionEngineService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:send-reminder')->everyMinute();
Schedule::command('app:generate-recurring-task')->daily();

// Action Engine: Detects conditions and executes automatic actions
// Runs every 5 minutes to check for: overdue tasks, missed follow-ups, inactive tasks, MOM items, follow-up loops
Schedule::call(function () {
    app(ActionEngineService::class)->run();
})->everyFiveMinutes()
  ->name('action-engine')
  ->withoutOverlapping();

