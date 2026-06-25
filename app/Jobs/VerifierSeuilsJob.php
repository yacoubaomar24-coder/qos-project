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
        $site = Site::with(['ville.region.pays'])->find($siteId);
        //$site = Site::find($siteId);
        
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
        // Collecter tous les destinataires selon la hiérarchie
        // -----------------------------------------------
        $destinataires = $this->getDestinataires($site);

        // Ajouter l'email du seuil si différent
        if ($seuil->notif_email && $seuil->email_destination) {
            $destinataires[] = $seuil->email_destination;
        }

        // Supprimer les doublons
        $destinataires = array_unique(array_filter($destinataires));
        
        if (empty($destinataires)) {
            \Illuminate\Support\Facades\Log::warning(
                "Aucun destinataire trouvé pour l'alerte du site : " . $site->nom
            );
            return;
        }

        // Envoyer à chaque destinataire
        foreach ($destinataires as $email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)
                    ->send(new \App\Mail\AlerteInsatisfactionMail($alerte, $site));

                \Illuminate\Support\Facades\Log::info(
                    "Email envoyé à : {$email} pour le site : {$site->nom}"
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error(
                    "Erreur envoi email à {$email} : " . $e->getMessage()
                );
            }
        }

        // Marquer l'email comme envoyé si au moins un envoi réussi
        $alerte->update(['email_envoye' => true]);

        /*
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
        }*/
    }

    // Récupérer tous les admins concernés par ce site
    // -----------------------------------------------
    private function getDestinataires(Site $site): array
    {
        $emails = [];

        // -----------------------------------------------
        // 1. Admin de site — directement lié au site
        // -----------------------------------------------
        $adminsSite = \App\Models\Utilisateur::where('role', 'Admin de site')
            ->where('site_id', $site->id)
            ->where('statut', true) // uniquement les comptes actifs
            ->pluck('email')
            ->toArray();
        $emails = array_merge($emails, $adminsSite);

        // 2. Admin régional — région du site
        // -----------------------------------------------
        $regionId = $site->ville?->region?->id;
        if ($regionId) {
            $adminsRegion = \App\Models\Utilisateur::where('role', 'Admin régional')
                ->where('region_id', $regionId)
                ->where('statut', true)
                ->pluck('email')
                ->toArray();
            $emails = array_merge($emails, $adminsRegion);
        }

        // 3. Admin national — pays du site
        // -----------------------------------------------
        $paysId = $site->ville?->region?->pays?->id;
        if ($paysId) {
            // Trouver les régions du pays
            $regionIds = \App\Models\Region::where('pays_id', $paysId)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');

            $adminsNational = \App\Models\Utilisateur::where('role', 'Admin national')
                ->where('statut', true)
                ->where(function ($q) use ($paysId) {
                    // Admin national lié à ce pays
                    $q->where('pays_id', $paysId);
                })
                ->pluck('email')
                ->toArray();
            $emails = array_merge($emails, $adminsNational);
        }

        // 4. Super admin — créateur du site
        // -----------------------------------------------
        if ($site->created_by) {
            $superAdmin = \App\Models\Utilisateur::where('id', $site->created_by)
                ->where('role', 'Super admin')
                ->where('statut', true)
                ->value('email');

            if ($superAdmin) {
                $emails[] = $superAdmin;
            }
            // Aussi le Super admin créateur des admins nationaux
            $superAdminViaAdmin = \App\Models\Utilisateur::where('role', 'Super admin')
                ->where('statut', true)
                ->whereHas('utilisateursCreés', function ($q) use ($site) {
                    $q->where('role', 'Admin national')
                    ->where('pays_id', $site->ville?->region?->pays?->id);
                })
                ->pluck('email')
                ->toArray();
            $emails = array_merge($emails, $superAdminViaAdmin);
        }

        return $emails;
    }
}
