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
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.alertes';

    // Toutes les propriétés doivent être déclarées
    public array $alertes = [];
    public array $seuils = [];
    public array $sitesOptions = [];
    public string $filtreStatut = 'toutes';

    // Propriétés pour le formulaire de seuil
    public ?int $seuilSiteId = null;
    public int $seuilPourcentage = 40;
    public int $seuilPeriode = 24;

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
        //$this->loadSeuils();
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

    // Renvoie du mail manuellement
    public function renvoyerNotification(int $alerteId): void
    {
        logger('renvoyerNotification appelé pour alerte: ' . $alerteId);

        //$alerte = \App\Models\Alerte::with('site', 'seuil')->find($alerteId);
        $alerte = \App\Models\Alerte::with(['site.ville.region.pays', 'seuil'])->find($alerteId);
        
        if (!$alerte) {
            logger('Alerte introuvable: ' . $alerteId);
            return;
        }

        logger('Alerte trouvée: ' . $alerte->site?->nom);

        // Récupérer tous les destinataires
        $destinataires = $this->getDestinataires($alerte->site);

        // Ajouter l'email du seuil
        if ($alerte->seuil?->notif_email && $alerte->seuil?->email_destination) {
            $destinataires[] = $alerte->seuil->email_destination;
        }

        $destinataires = array_unique(array_filter($destinataires));
        
        foreach ($destinataires as $email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)
                    ->send(new \App\Mail\AlerteInsatisfactionMail($alerte, $alerte->site));
                logger("Email renvoyé à : {$email}");
            } catch (\Exception $e) {
                logger("Erreur : " . $e->getMessage());
            }
        }

        $alerte->update(['email_envoye' => true]);
        $this->loadAlertes();
    }

    // Méthode commune pour récupérer les destinataires
    // -----------------------------------------------
    private function getDestinataires(Site $site): array
    {
        $emails = [];

        // Admin de site
        $emails = array_merge($emails,
            \App\Models\Utilisateur::where('role', 'Admin de site')
                ->where('site_id', $site->id)
                ->where('statut', true)
                ->pluck('email')->toArray()
        );

        // Admin régional
        $regionId = $site->ville?->region?->id;
        if ($regionId) {
            $emails = array_merge($emails,
                \App\Models\Utilisateur::where('role', 'Admin régional')
                    ->where('region_id', $regionId)
                    ->where('statut', true)
                    ->pluck('email')->toArray()
            );
        }

        // Admin national
        $paysId = $site->ville?->region?->pays?->id;
        if ($paysId) {
            $emails = array_merge($emails,
                \App\Models\Utilisateur::where('role', 'Admin national')
                    ->where('pays_id', $paysId)
                    ->where('statut', true)
                    ->pluck('email')->toArray()
            );
        }

        // Super admin créateur
        if ($site->created_by) {
            $superAdmin = \App\Models\Utilisateur::where('id', $site->created_by)
                ->where('role', 'Super admin')
                ->where('statut', true)
                ->value('email');

            if ($superAdmin) $emails[] = $superAdmin;
        }

        return array_unique(array_filter($emails));
    }
}
