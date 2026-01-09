<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:verificar-novos-usuarios')
    ->dailyAt('06:00')
    ->timezone('America/Sao_Paulo');

// Roda às 6:59 da manhã
Schedule::command('app:lembrete-frequencia-diaria')
    ->dailyAt('06:59')
    ->timezone('America/Sao_Paulo');

// Roda às 18:00
Schedule::command('app:lembrete-frequencia-diaria')
    ->dailyAt('18:00')
    ->timezone('America/Sao_Paulo');
