<?php

namespace App\Filament\Pages;

use App\Models\Alerte;
use App\Models\Seuil;
use App\Models\Site;
use App\Models\Utilisateur;
use Filament\Pages\Page;

class Alertes extends Page
{
    protected static ?string $navigationLabel = 'Alertes & Notifications';
    protected static ?string $title = '';
    protected static ?int    $navigationSort = 4;
    protected string $view = 'filament.pages.alertes';

    // Toutes les propriétés doivent être déclarées
    public array  $alertes      = [];
    public array  $seuils       = [];
    public array  $sitesOptions = [];
    public string $filtreStatut = 'toutes';

    // Propriétés pour le formulaire de seuil
    public ?int    $seuilSiteId      = null;
    public int     $seuilPourcentage = 40;
    public int     $seuilPeriode     = 24;
    public bool    $seuilEmail       = true;
    public bool    $seuilSms         = false;
    public ?string $seuilEmailDest   = null;
    public ?string $seuilSmsDest     = null;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bell-alert';
    }

    // Masquer à Admin principal
    public static function shouldRegisterNavigation(): bool
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return false;
        return !$user->hasRole('Admin');
    }

    public function mount(): void
    {
        $this->sitesOptions = $this->getSitesOptions();
        $this->loadAlertes();
        $this->loadSeuils();
    }

    // -----------------------------------------------
    // Sites accessibles selon le rôle
    // -----------------------------------------------
    private function getSitesOptions(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $query = Site::query()->where('statut', true);

        if ($user->hasRole('Super admin')) {
            $adminIds = Utilisateur::where('created_by', $user->id)
                ->where('role', 'Admin national')->pluck('id')->toArray();
            $query->whereIn('created_by', array_merge([$user->id], $adminIds));
        } elseif ($user->hasRole('Admin national')) {
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin de site')) {
            $query->where('id', $user->site_id);
        }

        return $query->pluck('nom', 'id')->toArray();
    }

    // -----------------------------------------------
    // Charger les alertes selon le filtre
    // -----------------------------------------------
    public function loadAlertes(): void
    {
        $siteIds = array_keys($this->sitesOptions);

        $query = Alerte::with(['site', 'seuil'])
            ->whereIn('site_id', $siteIds)
            ->latest();

        // Filtre par statut
        if ($this->filtreStatut !== 'toutes') {
            $query->where('statut', $this->filtreStatut);
        }

        $this->alertes = $query->limit(50)->get()->toArray();
    }

    // -----------------------------------------------
    // Charger les seuils configurés
    // -----------------------------------------------
    public function loadSeuils(): void
    {
        /** @var Utilisateur|null $user */
        $user    = filament()->auth()->user();
        $siteIds = array_keys($this->sitesOptions);

        $this->seuils = Seuil::with('site')
            ->where(function ($q) use ($siteIds, $user) {
                $q->whereIn('site_id', $siteIds)
                  ->orWhere(function ($q2) use ($user) {
                      // Seuils globaux créés par cet utilisateur
                      $q2->whereNull('site_id')
                         ->where('created_by', $user?->id);
                  });
            })
            ->get()
            ->toArray();
    }

    // -----------------------------------------------
    // Marquer une alerte comme vue
    // -----------------------------------------------
    public function marquerVue(int $alerteId): void
    {
        Alerte::find($alerteId)?->update(['statut' => 'vue']);
        $this->loadAlertes();
    }

    // -----------------------------------------------
    // Marquer une alerte comme résolue
    // -----------------------------------------------
    public function marquerResolue(int $alerteId): void
    {
        Alerte::find($alerteId)?->update(['statut' => 'resolue']);
        $this->loadAlertes();
    }

    // -----------------------------------------------
    // Changer le filtre statut
    // -----------------------------------------------
    public function changerFiltre(string $statut): void
    {
        $this->filtreStatut = $statut;
        $this->loadAlertes();
    }

    // -----------------------------------------------
    // Créer ou modifier un seuil
    // -----------------------------------------------
    public function sauvegarderSeuil(
        ?int    $siteId,
        int     $seuilInsatisfaction,
        int     $periodeHeures,
        bool    $notifEmail,
        bool    $notifSms,
        ?string $emailDestination,
        ?string $telephoneDestination
    ): void {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        Seuil::updateOrCreate(
            [
                'site_id'    => $siteId,
                'created_by' => $user?->id,
            ],
            [
                'seuil_insatisfaction'  => $seuilInsatisfaction,
                'periode_heures'        => $periodeHeures,
                'notif_email'           => $notifEmail,
                'notif_sms'             => $notifSms,
                'email_destination'     => $emailDestination,
                'telephone_destination' => $telephoneDestination,
                'actif'                 => true,
            ]
        );

        $this->loadSeuils();
    }

    // -----------------------------------------------
    // Tester manuellement la vérification des seuils
    // -----------------------------------------------
    public function testerSeuils(): void
    {
        //\App\Jobs\VerifierSeuilsJob::dispatch();
        \App\Jobs\VerifierSeuilsJob::dispatchSync();
        $this->loadAlertes();
    }

    // Renvoie du mail manuellement
    public function renvoyerNotification(int $alerteId): void
    {
        logger('renvoyerNotification appelé pour alerte: ' . $alerteId);

        $alerte = \App\Models\Alerte::with('site', 'seuil')->find($alerteId);
        
        if (!$alerte) {
            logger('Alerte introuvable: ' . $alerteId);
            return;
        }

        logger('Alerte trouvée: ' . $alerte->site?->nom);
        logger('Seuil email: ' . ($alerte->seuil?->notif_email ? 'true' : 'false'));
        logger('Email dest: ' . ($alerte->seuil?->email_destination ?? 'null'));

        // Renvoyer l'email
        if ($alerte->seuil?->notif_email && $alerte->seuil?->email_destination) {
            try {
                \Illuminate\Support\Facades\Mail::to($alerte->seuil->email_destination)
                    ->send(new \App\Mail\AlerteInsatisfactionMail($alerte, $alerte->site));

                $alerte->update(['email_envoye' => true]);

                logger('Email renvoyé avec succès !');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur mail : ' . $e->getMessage());
            }
        }

        $this->loadAlertes();
    }
}
