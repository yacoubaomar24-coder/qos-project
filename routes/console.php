<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\VerifierSeuilsJob;
use Illuminate\Support\Facades\Schedule;

// Vérifier les seuils toutes les heures
Schedule::job(new VerifierSeuilsJob())->hourly();

// Ou toutes les 15 minutes pour plus de réactivité
Schedule::job(new VerifierSeuilsJob())->everyFifteenMinutes();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
