<?php
// app/Jobs/EnvoyerRapportsAutoJob.php

namespace App\Jobs;

use App\Mail\RapportAutomatiqueMail;
use App\Models\RapportAuto;
use App\Models\Site;
use App\Models\Vote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnvoyerRapportsAutoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        // Fréquence à traiter : quotidien, hebdomadaire, mensuel
        private string $frequence
    ) {}

    public function handle(): void
    {
        Log::info("EnvoyerRapportsAutoJob démarré — fréquence : {$this->frequence}");

        // Récupérer tous les rapports actifs pour cette fréquence
        $rapports = RapportAuto::where('frequence', $this->frequence)
            ->where('actif', true)
            ->get();

        if ($rapports->isEmpty()) {
            Log::info("Aucun rapport configuré pour : {$this->frequence}");
            return;
        }

        foreach ($rapports as $rapport) {
            $this->envoyerRapport($rapport);
        }
    }

    private function envoyerRapport(RapportAuto $rapport): void
    {
        // -----------------------------------------------
        // Calculer les dates selon la fréquence
        // -----------------------------------------------
        [$debut, $fin] = match ($rapport->frequence) {
            'quotidien'    => [
                now()->subDay()->startOfDay(),
                now()->subDay()->endOfDay(),
            ],
            'hebdomadaire' => [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek(),
            ],
            'mensuel'      => [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ],
            default => [now()->startOfDay(), now()->endOfDay()],
        };

        // -----------------------------------------------
        // Déterminer les sites à inclure
        // -----------------------------------------------
        $siteIds = $rapport->site_ids
            ?? Site::pluck('id')->toArray(); // null = tous les sites

        // -----------------------------------------------
        // Collecter les données pour chaque site
        // -----------------------------------------------
        $donnees = [];

        foreach ($siteIds as $siteId) {
            $site = Site::with(['ville.region'])->find($siteId);
            if (!$site) continue;

            $query = Vote::where('site_id', $siteId)->whereBetween('created_at', [$debut, $fin]);
            $total = (clone $query)->count();
            $satisfaits = (clone $query)->where('niveau', 'satisfait')->count();
            $moyens = (clone $query)->where('niveau', 'moyen')->count();
            $insatisfaits = (clone $query)->where('niveau', 'insatisfait')->count();

            // Inclure même les sites sans votes
            $donnees[] = [
                'site' => $site->nom,
                'region' => $site->ville?->region?->nom ?? 'N/A',
                'total' => $total,
                'satisfaits' => $satisfaits,
                'moyens' => $moyens,
                'insatisfaits' => $insatisfaits,
                'taux_satisfaction' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                'taux_moyen' => $total > 0 ? round(($moyens / $total) * 100, 1) : 0,
                'taux_insatisfaction' => $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0,
                
            ];
        }

        // Trier par taux décroissant
        usort($donnees, fn($a, $b) => $b['taux_satisfaction'] <=> $a['taux_satisfaction']);

        // -----------------------------------------------
        // Envoyer le mail
        // -----------------------------------------------
        try {
            Mail::to($rapport->email_destination)
                ->send(new RapportAutomatiqueMail(
                    rapport : $rapport,
                    donnees : $donnees,
                    debut : $debut->format('d/m/Y'),
                    fin : $fin->format('d/m/Y'),
                    frequence : $rapport->frequence,
                ));

            // Mettre à jour la dernière exécution
            $rapport->update(['derniere_execution' => now()]);

            Log::info("Rapport {$rapport->frequence} envoyé à : {$rapport->email_destination}");

        } catch (\Exception $e) {
            Log::error("Erreur envoi rapport : " . $e->getMessage());
        }
    }
}