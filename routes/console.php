<?php

use App\Jobs\CheckOverdueExpenses;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendar verificação de despesas vencidas diariamente às 00:01
Schedule::job(new CheckOverdueExpenses)->dailyAt('00:01');
