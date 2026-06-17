<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Exécute et envoie le bilan comptable au gérant automatiquement chaque soir à 23h00
Schedule::command('rapport:send-proprietaire')->dailyAt('23:00');
