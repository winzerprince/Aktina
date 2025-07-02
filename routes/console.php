<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automated report generation
Schedule::command('reports:generate')->daily()->at('02:00');
Schedule::command('reports:generate --cleanup')->weekly();
