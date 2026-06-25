<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\VerifierSeuilsJob;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\EnvoyerRapportsAutoJob;

// Vérifier les seuils toutes les heures
Schedule::job(new VerifierSeuilsJob())->hourly();

// Ou toutes les 15 minutes pour plus de réactivité
//Schedule::job(new VerifierSeuilsJob())->everyFifteenMinutes();

// Chaque minute
//Schedule::job(new VerifierSeuilsJob())->everyMinute()->name('verifier-seuils');

// Rapport quotidien — chaque jour à 8h00
Schedule::job(new EnvoyerRapportsAutoJob('quotidien'))
    ->dailyAt('08:00')
    ->name('rapport-quotidien');

// Rapport hebdomadaire — chaque lundi à 8h00
Schedule::job(new EnvoyerRapportsAutoJob('hebdomadaire'))
    ->weeklyOn(1, '08:00')
    ->name('rapport-hebdomadaire');

// Rapport mensuel — le 1er de chaque mois à 8h00
Schedule::job(new EnvoyerRapportsAutoJob('mensuel'))
    ->monthlyOn(1, '08:00')
    ->name('rapport-mensuel');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
