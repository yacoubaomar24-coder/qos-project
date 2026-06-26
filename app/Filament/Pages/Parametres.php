<?php
// app/Filament/Pages/Parametres.php

namespace App\Filament\Pages;

use App\Models\Configuration;
use App\Models\Utilisateur;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class Parametres extends Page
{
    protected static ?string $navigationLabel = 'Paramètres';
    protected static ?string $title           = 'Paramètres & Configuration';
    protected static ?int    $navigationSort  = 6;
    protected string         $view            = 'filament.pages.parametres';

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
    public ?string $organisationLogo      = null;
    public string  $couleurPrimaire       = '#f59e0b';
    public string  $couleurSecondaire     = '#111827';
    public ?string $logoActuel            = null;

    // Message de confirmation
    public string $message = '';

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

        Configuration::updateOrCreate(
            ['created_by' => $user?->id],
            [
                'organisation_nom'    => $this->organisationNom,
                'couleur_primaire'    => $this->couleurPrimaire,
                'couleur_secondaire'  => $this->couleurSecondaire,
            ]
        );

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
        $heureActuelle = $maintenant->format('H:i');
        $jourActuel   = $maintenant->dayOfWeek; // 0=dim, 1=lun, ..., 6=sam

        // Vérifier le jour
        $joursActifs = $config->jours_actifs ?? [1, 2, 3, 4, 5];
        if (!in_array($jourActuel, $joursActifs)) {
            return false;
        }

        // Vérifier l'heure
        $heureDebut = substr($config->heure_debut, 0, 5);
        $heureFin   = substr($config->heure_fin, 0, 5);

        return $heureActuelle >= $heureDebut && $heureActuelle <= $heureFin;
    }
}