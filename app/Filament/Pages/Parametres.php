<?php
// app/Filament/Pages/Parametres.php

namespace App\Filament\Pages;

use App\Models\Configuration;
use App\Models\Utilisateur;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads; 

class Parametres extends Page
{
    use WithFileUploads;
    protected static ?string $navigationLabel = 'Paramètres';
    protected static ?string $title = '';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.parametres';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    // Visible uniquement pour Super admin et Admin national
    public static function shouldRegisterNavigation(): bool
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->hasAnyRole(['Super admin', 'Admin national']);
    }

    // -----------------------------------------------
    // Libellés des boutons IoT
    // -----------------------------------------------
    public string $libellesatisfait   = 'Satisfait';
    public string $libelleMoyen     = 'Moyennement satisfait';
    public string $libelleInsatisfait = 'Insatisfait';

    // -----------------------------------------------
    // Couleurs des boutons IoT
    // -----------------------------------------------
    public string $couleurSatisfait   = '#22c55e';
    public string $couleurMoyen      = '#f59e0b';
    public string $couleurInsatisfait = '#ef4444';

    // -----------------------------------------------
    // Plages horaires d'activité
    // -----------------------------------------------
    public string $heureDebut  = '08:00';
    public string $heureFin    = '17:00';
    public array  $joursActifs = [1, 2, 3, 4, 5]; // Lun→Ven

    // -----------------------------------------------
    // Personnalisation interface
    // -----------------------------------------------
    public string  $organisationNom       = 'Mon Organisation';
    //public ?string $organisationLogo      = null;
    public $organisationLogo = null;
    public string  $couleurPrimaire       = '#f59e0b';
    public string  $couleurSecondaire     = '#111827';
    public ?string $logoActuel            = null;

    // Message de confirmation
    public string $message = '';

    public ?int    $seuilSiteId      = null;
    public int     $seuilPourcentage = 40;
    public int     $seuilPeriode     = 24;
    public bool    $seuilEmail       = true;
    public array   $seuils           = [];
    public array   $sitesOptions     = [];

    public function mount(): void
    {
        // Charger la configuration existante
        /** @var Utilisateur|null $user */
        $user   = filament()->auth()->user();
        $config = Configuration::where('created_by', $user?->id)->first();

        if ($config) {
            // Remplir les propriétés avec les valeurs sauvegardées
            $this->libellesatisfait    = $config->libelle_satisfait;
            $this->libelleMoyen      = $config->libelle_moyen;
            $this->libelleInsatisfait = $config->libelle_insatisfait;
            $this->couleurSatisfait   = $config->couleur_satisfait;
            $this->couleurMoyen      = $config->couleur_moyen;
            $this->couleurInsatisfait = $config->couleur_insatisfait;
            $this->heureDebut         = substr($config->heure_debut, 0, 5);
            $this->heureFin           = substr($config->heure_fin, 0, 5);
            $this->joursActifs        = $config->jours_actifs ?? [1, 2, 3, 4, 5];
            $this->organisationNom    = $config->organisation_nom;
            $this->couleurPrimaire    = $config->couleur_primaire;
            $this->couleurSecondaire  = $config->couleur_secondaire;
            $this->logoActuel         = $config->organisation_logo;
        }

        // Charger les sites et seuils
        $this->sitesOptions = $this->getSitesOptions();
        $this->loadSeuils();
    }

    // -----------------------------------------------
    // Sauvegarder les libellés des boutons
    // -----------------------------------------------
    public function sauvegarderLibelles(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        Configuration::updateOrCreate(
            ['created_by' => $user?->id],
            [
                'libelle_satisfait'   => $this->libellesatisfait,
                'libelle_moyen'      => $this->libelleMoyen,
                'libelle_insatisfait' => $this->libelleInsatisfait,
                'couleur_satisfait'   => $this->couleurSatisfait,
                'couleur_moyen'      => $this->couleurMoyen,
                'couleur_insatisfait' => $this->couleurInsatisfait,
            ]
        );

        $this->message = 'Libellés sauvegardés avec succès !';
    }

    // -----------------------------------------------
    // Sauvegarder les plages horaires
    // -----------------------------------------------
    public function sauvegarderHoraires(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        // Validation basique
        if ($this->heureDebut >= $this->heureFin) {
            $this->message = 'Erreur : l\'heure de début doit être avant l\'heure de fin.';
            return;
        }

        Configuration::updateOrCreate(
            ['created_by' => $user?->id],
            [
                'heure_debut'  => $this->heureDebut,
                'heure_fin'    => $this->heureFin,
                'jours_actifs' => $this->joursActifs,
            ]
        );

        $this->message = 'Plages horaires sauvegardées avec succès !';
    }

    // -----------------------------------------------
    // Sauvegarder la personnalisation interface
    // -----------------------------------------------
    public function sauvegarderInterface(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        $data = [
            'organisation_nom'   => $this->organisationNom,
            'couleur_primaire'   => $this->couleurPrimaire,
            'couleur_secondaire' => $this->couleurSecondaire,
        ];

        // ✅ Gérer l'upload du logo
        if ($this->organisationLogo) {
            // Supprimer l'ancien logo
            if ($this->logoActuel) {
                Storage::disk('public')->delete($this->logoActuel);
            }

            // Sauvegarder le nouveau logo dans storage/app/public/logos/
            $chemin = $this->organisationLogo->store('logos', 'public');
            $data['organisation_logo'] = $chemin;
            $this->logoActuel          = $chemin;

            // Réinitialiser le champ
            $this->organisationLogo = null;
        }
        
        Configuration::updateOrCreate(
            ['created_by' => $user?->id],
            $data
        );
        /*
        Configuration::updateOrCreate(
            ['created_by' => $user?->id],
            [
                'organisation_nom'    => $this->organisationNom,
                'couleur_primaire'    => $this->couleurPrimaire,
                'couleur_secondaire'  => $this->couleurSecondaire,
            ]
        );*/

        $this->message = 'Interface personnalisée avec succès !';
    }

    // -----------------------------------------------
    // Uploader le logo
    // -----------------------------------------------
    public function sauvegarderLogo(array $logo): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        // Supprimer l'ancien logo
        if ($this->logoActuel) {
            Storage::disk('public')->delete($this->logoActuel);
        }

        // Sauvegarder le nouveau logo
        $chemin = $logo[0] ?? null;
        if ($chemin) {
            Configuration::updateOrCreate(
                ['created_by' => $user?->id],
                ['organisation_logo' => $chemin]
            );
            $this->logoActuel = $chemin;
            $this->message    = 'Logo mis à jour avec succès !';
        }
    }

    // -----------------------------------------------
    // Vérifier si un dispositif est actif maintenant
    // Utilisée par l'API pour valider les votes
    // -----------------------------------------------
    public static function dispositifEstActif(?int $userId = null): bool
    {
        $config = Configuration::where('created_by', $userId)->first();

        if (!$config) return true; // Pas de config = toujours actif

        $maintenant   = now();
        //$heureActuelle = $maintenant->format('H:i');

        // Conversion du format h:mn en mn
        [$hNow, $mNow] = explode(':', now()->format('H:i'));
        $heureActuelle = ((int) $hNow * 60) + (int) $mNow;

        //$jourActuel   = $maintenant->dayOfWeek; // 0=dim, 1=lun, ..., 6=sam
        $jourActuel    = (int) $maintenant->format('N'); // 1=Lun, 7=Dim (ISO-8601)

        // ✅ Convertir le jour ISO en format stocké (0=Dim, 1=Lun, ..., 6=Sam)
        $jourConverti = $jourActuel === 7 ? 0 : $jourActuel;

        $joursActifs = $config->jours_actifs ?? [1, 2, 3, 4, 5];
        
        // Vérifier le jour
        if (!in_array($jourConverti, $joursActifs)) {
            \Illuminate\Support\Facades\Log::info(
                "Dispositif inactif — jour {$jourConverti} non configuré. Jours actifs: " .
                implode(',', $joursActifs)
            );
            return false;
        }

        // Vérifier l'heure
        //$heureDebut = substr($config->heure_debut, 0, 5);
        //$heureFin   = substr($config->heure_fin, 0, 5);
        [$hDebut, $mDebut] = explode(':', substr($config->heure_debut, 0, 5));
        $heureDebut = ((int) $hDebut * 60) + (int) $mDebut;

        [$hFin, $mFin] = explode(':', substr($config->heure_fin, 0, 5));
        $heureFin = ((int) $hFin * 60) + (int) $mFin;

        $actif = $heureActuelle >= $heureDebut && $heureActuelle <= $heureFin;

        \Illuminate\Support\Facades\Log::info(
            "Vérification horaire — maintenant: {$heureActuelle} " .
            "plage: {$heureDebut}→{$heureFin} — actif: " . ($actif ? 'oui' : 'non')
        );

        return $actif;
    }

    // -----------------------------------------------
    // Sites accessibles selon le rôle
    // -----------------------------------------------
    private function getSitesOptions(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $query = \App\Models\Site::query()->where('statut', true);

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

    // Charger les seuils
    public function loadSeuils(): void
    {
        /** @var Utilisateur|null $user */
        $user    = filament()->auth()->user();
        $siteIds = array_keys($this->sitesOptions);

        $this->seuils = \App\Models\Seuil::with('site')
            ->where(function ($q) use ($siteIds, $user) {
                $q->whereIn('site_id', $siteIds)
                ->orWhere(function ($q2) use ($user) {
                    $q2->whereNull('site_id')
                        ->where('created_by', $user?->id);
                });
            })
            ->get()->toArray();
    }

    // Sauvegarder un seuil
    public function sauvegarderSeuil(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        \App\Models\Seuil::updateOrCreate(
            ['site_id' => $this->seuilSiteId ?: null, 'created_by' => $user?->id],
            [
                'seuil_insatisfaction' => $this->seuilPourcentage,
                'periode_heures'       => $this->seuilPeriode,
                'notif_email'          => true,
                'actif'                => true,
            ]
        );
        $this->message = 'Seuil sauvegardé avec succès !';
        $this->loadSeuils();
    }

    public function modifierSeuil(int $seuilId): void
    {
        $seuil = \App\Models\Seuil::find($seuilId);
        if (!$seuil) return;
        $this->seuilSiteId      = $seuil->site_id;
        $this->seuilPourcentage = $seuil->seuil_insatisfaction;
        $this->seuilPeriode     = $seuil->periode_heures;
    }

    public function toggleSeuil(int $seuilId): void
    {
        $seuil = \App\Models\Seuil::find($seuilId);
        if (!$seuil) return;
        $seuil->update(['actif' => !$seuil->actif]);
        $this->loadSeuils();
    }
}