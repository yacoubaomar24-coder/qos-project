<?php

namespace App\Jobs;

use App\Models\Alerte;
use App\Models\Seuil;
use App\Models\Site;
use App\Models\Vote;
use App\Notifications\AlerteInsatisfactionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class VerifierSeuilsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('VerifierSeuilsJob démarré à ' . now());

        // Récupérer tous les seuils actifs
        $seuils = Seuil::where('actif', true)->get();

        foreach ($seuils as $seuil) {
            // Déterminer les sites à vérifier
            $siteIds = $seuil->site_id
                ? [$seuil->site_id]          // Seuil spécifique à un site
                : Site::pluck('id')->toArray(); // Seuil global — tous les sites

            foreach ($siteIds as $siteId) {
                $this->verifierSite($siteId, $seuil);
            }
        }

        Log::info('VerifierSeuilsJob terminé à ' . now());
    }

    // -----------------------------------------------
    // Vérifier un site par rapport à un seuil
    // -----------------------------------------------
    private function verifierSite(int $siteId, Seuil $seuil): void
    {
        $site = Site::find($siteId);
        if (!$site) return;

        // Calculer le taux d'insatisfaction sur la période configurée
        $debut = now()->subHours($seuil->periode_heures);

        $totalVotes = Vote::where('site_id', $siteId)
            ->where('created_at', '>=', $debut)
            ->count();

        // Ignorer si moins de 5 votes — pas assez représentatif
        if ($totalVotes < 5) return;

        $insatisfaits = Vote::where('site_id', $siteId)
            ->where('created_at', '>=', $debut)
            ->where('niveau', 'insatisfait')
            ->count();
        
        $taux = round(($insatisfaits / $totalVotes) * 100, 2);

        // -----------------------------------------------
        // Vérifier si le seuil est dépassé
        // -----------------------------------------------
        if ($taux >= $seuil->seuil_insatisfaction) {

            // Éviter les doublons — vérifier si une alerte existe déjà
            // pour ce site dans la dernière heure
            $alerteExistante = Alerte::where('site_id', $siteId)
                ->where('seuil_id', $seuil->id)
                ->where('created_at', '>=', now()->subHour())
                ->exists();

            if ($alerteExistante) {
                Log::info("Alerte déjà envoyée pour {$site->nom} — ignorée");
                return;
            }
            // Créer l'alerte
            $message = "Le site {$site->nom} a atteint un taux d'insatisfaction " .
                       "de {$taux}% sur les {$seuil->periode_heures} dernières heures " .
                       "(seuil configuré : {$seuil->seuil_insatisfaction}%)";

            $alerte = Alerte::create([
                'site_id'            => $siteId,
                'seuil_id'           => $seuil->id,
                'taux_insatisfaction' => $taux,
                'seuil_configure'    => $seuil->seuil_insatisfaction,
                'total_votes'        => $totalVotes,
                'statut'             => 'nouvelle',
                'message'            => $message,
            ]);

            Log::warning("Alerte créée : {$message}");
            // Envoyer les notifications
            $this->envoyerNotifications($alerte, $seuil, $site);
        }   
    }

    // -----------------------------------------------
    // Envoyer les notifications email/SMS
    // -----------------------------------------------
    private function envoyerNotifications(Alerte $alerte, Seuil $seuil, Site $site): void
    {
        // Notification email
        if ($seuil->notif_email && $seuil->email_destination) {
            try {
                Log::info("Tentative envoi email à : " . $seuil->email_destination);
                \Illuminate\Support\Facades\Mail::to($seuil->email_destination)
                    ->send(new \App\Mail\AlerteInsatisfactionMail($alerte, $site));

                $alerte->update(['email_envoye' => true]);
                Log::info("Email envoyé à {$seuil->email_destination}");
            } catch (\Exception $e) {
                // ✅ Logger l'erreur complète
                Log::error("Erreur d'envoi d'email : " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        } else {
            Log::warning("Email non envoyé — notif_email: " . ($seuil->notif_email ? 'true' : 'false') .
                     " — email_destination: " . ($seuil->email_destination ?? 'null'));
        }
        // Notification SMS (via API externe — ex: Twilio, Orange SMS)
        if ($seuil->notif_sms && $seuil->telephone_destination) {
            try {
                // À adapter selon ton provider SMS
                $this->envoyerSms($seuil->telephone_destination, $alerte->message);
                $alerte->update(['sms_envoye' => true]);
                Log::info("SMS envoyé à {$seuil->telephone_destination}");
            } catch (\Exception $e) {
                Log::error("Erreur SMS : " . $e->getMessage());
            }
        }
    }

    // -----------------------------------------------
    // Envoyer un SMS — à adapter selon le provider
    // -----------------------------------------------
    private function envoyerSms(string $telephone, string $message): void
    {
        // Exemple avec une API HTTP générique
        // Remplace par Twilio, Orange SMS, etc.
        \Illuminate\Support\Facades\Http::post('https://api.sms-provider.com/send', [
            'to'      => $telephone,
            'message' => $message,
            'api_key' => config('services.sms.key'),
        ]);
    }
}
