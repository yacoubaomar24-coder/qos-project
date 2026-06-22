<?php
// app/Console/Commands/VerifierSeuils.php

namespace App\Console\Commands;

use App\Jobs\VerifierSeuilsJob;
use Illuminate\Console\Command;

class VerifierSeuils extends Command
{
    protected $signature   = 'seuils:verifier';
    protected $description = 'Vérifier les seuils d insatisfaction et déclencher les alertes';

    public function handle(): void
    {
        $this->info('Vérification des seuils en cours...');
        
        // Exécuter le job synchroniquement
        (new VerifierSeuilsJob())->handle();
        
        $this->info('Vérification terminée !');
        
        // Afficher les nouvelles alertes
        $alertes = \App\Models\Alerte::where('statut', 'nouvelle')
            ->with('site')
            ->latest()
            ->get();

        if ($alertes->isEmpty()) {
            $this->info('✅ Aucune nouvelle alerte.');
        } else {
            $this->warn("🚨 {$alertes->count()} nouvelle(s) alerte(s) :");
            foreach ($alertes as $alerte) {
                $this->error(
                    "  → {$alerte->site->nom} : {$alerte->taux_insatisfaction}% " .
                    "(seuil : {$alerte->seuil_configure}%)"
                );
            }
        }
    }
}